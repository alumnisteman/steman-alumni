#!/bin/bash
# ================================================================
# STEMAN ALUMNI — Auto-Recovery Script
# Deteksi dan fix otomatis error 502, 503, 526, 404
# Jalankan via cron: */2 * * * * /var/www/steman-alumni/scripts/auto-recovery.sh
# ================================================================

APP_DIR="/var/www/steman-alumni"
LOG_FILE="$APP_DIR/storage/logs/auto-recovery.log"
COMPOSE_FILE="$APP_DIR/docker-compose.prod.yml"
TELEGRAM_TOKEN=$(grep '^TELEGRAM_BOT_TOKEN' "$APP_DIR/.env" 2>/dev/null | cut -d= -f2 | tr -d '\r')
TELEGRAM_CHAT=$(grep '^TELEGRAM_CHAT_ID' "$APP_DIR/.env" 2>/dev/null | cut -d= -f2 | tr -d '\r')
LOCK_FILE="/tmp/steman-recovery.lock"
MAX_LOCK_AGE=300  # 5 menit

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

notify() {
    local msg="$1"
    log "NOTIF: $msg"
    if [ -n "$TELEGRAM_TOKEN" ] && [ -n "$TELEGRAM_CHAT" ]; then
        curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage" \
            -d "chat_id=${TELEGRAM_CHAT}" \
            -d "text=🚨 [Auto-Recovery] ${msg}" \
            --max-time 10 > /dev/null 2>&1 || true
    fi
}

# Cegah concurrent execution
if [ -f "$LOCK_FILE" ]; then
    LOCK_AGE=$(( $(date +%s) - $(stat -c %Y "$LOCK_FILE" 2>/dev/null || echo 0) ))
    if [ "$LOCK_AGE" -lt "$MAX_LOCK_AGE" ]; then
        exit 0
    fi
    rm -f "$LOCK_FILE"
fi
touch "$LOCK_FILE"
trap "rm -f $LOCK_FILE" EXIT

cd "$APP_DIR" || exit 1

# ────────────────────────────────────────────────
# 1. Cek HTTP Status (internal)
# ────────────────────────────────────────────────
HTTP_CODE=$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 http://localhost/health 2>/dev/null)
SITE_DOWN=0

if [ "$HTTP_CODE" != "200" ]; then
    SITE_DOWN=1
    log "ERROR: Site internal mengembalikan HTTP $HTTP_CODE (bukan 200)"
fi

# ────────────────────────────────────────────────
# 2. Fix 502 — PHP-FPM atau App container mati
# ────────────────────────────────────────────────
fix_502() {
    log "FIXING 502: Memeriksa container app dan nginx..."

    APP_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_app 2>/dev/null)
    NGINX_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_nginx 2>/dev/null)
    QUEUE_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_queue 2>/dev/null)

    # Jika app container unhealthy atau tidak ada
    if [ "$APP_STATUS" != "healthy" ]; then
        log "FIXING: steman_app tidak healthy ($APP_STATUS) — restart..."
        docker-compose -f "$COMPOSE_FILE" restart app 2>&1 | tail -3
        sleep 15
        NEW_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_app 2>/dev/null)
        notify "steman_app di-restart (was: $APP_STATUS, now: $NEW_STATUS). HTTP was $HTTP_CODE."
    fi

    # Jika nginx unhealthy
    if [ "$NGINX_STATUS" != "healthy" ]; then
        log "FIXING: steman_nginx tidak healthy ($NGINX_STATUS) — restart..."
        docker-compose -f "$COMPOSE_FILE" restart webserver 2>&1 | tail -3
        sleep 5
        notify "steman_nginx di-restart (was: $NGINX_STATUS)."
    fi

    # Queue worker mati
    if [ "$QUEUE_STATUS" != "healthy" ]; then
        log "FIXING: steman_queue tidak healthy ($QUEUE_STATUS) — restart..."
        docker-compose -f "$COMPOSE_FILE" restart queue 2>&1 | tail -3
        notify "steman_queue di-restart (was: $QUEUE_STATUS)."
    fi

    # Re-cek setelah fix
    NEW_HTTP=$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 http://localhost/health 2>/dev/null)
    if [ "$NEW_HTTP" = "200" ]; then
        log "RECOVERED: Site kembali normal (HTTP 200) setelah restart containers."
        notify "✅ Site RECOVERED: HTTP 200 setelah restart containers."
    else
        log "ERROR: Site masih HTTP $NEW_HTTP setelah restart. Perlu pengecekan manual!"
        notify "❌ Site MASIH ERROR: HTTP $NEW_HTTP setelah auto-fix. Perlu pengecekan manual!"
    fi
}

# ────────────────────────────────────────────────
# 3. Fix 503 — PHP-FPM worker habis (pool exhausted)
# ────────────────────────────────────────────────
fix_503() {
    log "FIXING 503: PHP-FPM worker mungkin habis — reload php-fpm..."
    docker exec steman_app kill -USR2 1 2>/dev/null || \
    docker exec steman_app php-fpm --force-stderr --nodaemonize -R 2>/dev/null &
    sleep 3
    # Tambah: bersihkan cache yang bisa memblokir
    docker exec steman_app php artisan cache:clear 2>/dev/null || true
    notify "503 detected: PHP-FPM di-reload, cache dibersihkan."
}

# ────────────────────────────────────────────────
# 4. Fix 526 — SSL Invalid (Cloudflare)
# ────────────────────────────────────────────────
fix_526() {
    log "CHECKING 526: Cek SSL cert validity..."
    CERT_FILE="$APP_DIR/docker/nginx/ssl/origin_cert_rsa.pem"
    if [ -f "$CERT_FILE" ]; then
        # Cek apakah cert belum expired
        EXPIRY=$(openssl x509 -in "$CERT_FILE" -noout -enddate 2>/dev/null | cut -d= -f2)
        EXPIRY_EPOCH=$(date -d "$EXPIRY" +%s 2>/dev/null || echo 9999999999)
        NOW_EPOCH=$(date +%s)
        DAYS_LEFT=$(( (EXPIRY_EPOCH - NOW_EPOCH) / 86400 ))

        if [ "$DAYS_LEFT" -lt 30 ]; then
            log "WARNING: SSL cert akan expired dalam $DAYS_LEFT hari! Perlu renewal!"
            notify "⚠️ SSL cert expires dalam $DAYS_LEFT hari! Segera renewal Cloudflare Origin Certificate."
        else
            log "SSL cert valid ($DAYS_LEFT hari tersisa). 526 mungkin sementara dari Cloudflare."
            # Reload nginx untuk refresh SSL
            docker exec steman_nginx nginx -s reload 2>/dev/null
            notify "526 detected: Nginx di-reload SSL. Cert valid $DAYS_LEFT hari."
        fi
    else
        log "ERROR: SSL cert tidak ditemukan di $CERT_FILE!"
        notify "❌ SSL cert TIDAK ADA di $CERT_FILE!"
    fi
}

# ────────────────────────────────────────────────
# 5. Fix Disk Full — auto cleanup
# ────────────────────────────────────────────────
check_disk() {
    DISK_USAGE=$(df / | awk 'NR==2{print $5}' | tr -d '%')
    if [ "$DISK_USAGE" -gt 85 ]; then
        log "WARNING: Disk usage $DISK_USAGE%! Membersihkan logs dan cache Docker..."
        # Bersihkan log lama
        find "$APP_DIR/storage/logs" -name "*.log" -mtime +7 -delete 2>/dev/null
        # Bersihkan Docker images dan containers yang tidak terpakai
        docker system prune -f --volumes=false 2>/dev/null | tail -2
        # Bersihkan compiled views
        docker exec steman_app php artisan view:clear 2>/dev/null || true
        DISK_AFTER=$(df / | awk 'NR==2{print $5}' | tr -d '%')
        notify "⚠️ Disk $DISK_USAGE% → cleanup → $DISK_AFTER%"
    fi
}

# ────────────────────────────────────────────────
# 6. Check DB & Redis — auto restart jika mati
# ────────────────────────────────────────────────
check_db_redis() {
    DB_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_db 2>/dev/null)
    REDIS_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_redis 2>/dev/null)

    if [ "$DB_STATUS" != "healthy" ]; then
        log "CRITICAL: steman_db tidak healthy ($DB_STATUS)! Restart..."
        docker-compose -f "$COMPOSE_FILE" restart db 2>&1 | tail -3
        sleep 20
        notify "🔴 Database di-restart! Status was: $DB_STATUS"
    fi

    if [ "$REDIS_STATUS" != "healthy" ]; then
        log "CRITICAL: steman_redis tidak healthy ($REDIS_STATUS)! Restart..."
        docker-compose -f "$COMPOSE_FILE" restart redis 2>&1 | tail -3
        sleep 5
        notify "🔴 Redis di-restart! Status was: $REDIS_STATUS"
    fi
}

# ────────────────────────────────────────────────
# Eksekusi berdasarkan kondisi
# ────────────────────────────────────────────────
check_disk
check_db_redis

if [ "$SITE_DOWN" = "1" ]; then
    if [ "$HTTP_CODE" = "502" ]; then
        fix_502
    elif [ "$HTTP_CODE" = "503" ]; then
        fix_503
    elif [ "$HTTP_CODE" = "526" ]; then
        fix_526
    else
        log "Site down dengan kode $HTTP_CODE — mencoba restart app container..."
        fix_502  # Generic restart
    fi
else
    log "OK: Site sehat (HTTP $HTTP_CODE)"
fi

# Batasi ukuran log recovery (maks 5MB)
if [ -f "$LOG_FILE" ] && [ $(stat -c%s "$LOG_FILE" 2>/dev/null || echo 0) -gt 5242880 ]; then
    tail -1000 "$LOG_FILE" > "$LOG_FILE.tmp" && mv "$LOG_FILE.tmp" "$LOG_FILE"
fi

exit 0
