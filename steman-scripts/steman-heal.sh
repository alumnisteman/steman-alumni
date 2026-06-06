#!/bin/bash
# ============================================================
# steman-heal.sh — Auto Repair Script untuk steman-alumni
# Jalankan via cron setiap 5 menit:
#   */5 * * * * /usr/local/bin/steman-heal.sh
# ============================================================

APP_DIR="/var/www/steman-alumni"
LOG_FILE="/var/log/steman-heal.log"
HEALTH_URL="https://alumni-steman.my.id/up"

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') $1" >> "$LOG_FILE"
}

check_container_health() {
    docker inspect --format='{{.State.Health.Status}}' "$1" 2>/dev/null
}

cd "$APP_DIR" || { log "ERROR: Direktori $APP_DIR tidak ditemukan"; exit 1; }

log "--- Memulai pengecekan kesehatan sistem ---"

APP_STATUS=$(check_container_health steman_app)
DB_STATUS=$(check_container_health steman_db)
REDIS_STATUS=$(check_container_health steman_redis)
NGINX_STATUS=$(check_container_health steman_nginx)

log "Status: app=$APP_STATUS | db=$DB_STATUS | redis=$REDIS_STATUS | nginx=$NGINX_STATUS"

NEED_REPAIR=false

if [ "$APP_STATUS" != "healthy" ]; then
    log "PERINGATAN: Container app tidak sehat (status: $APP_STATUS)"
    NEED_REPAIR=true
fi

if [ "$DB_STATUS" != "healthy" ]; then
    log "PERINGATAN: Container db tidak sehat (status: $DB_STATUS)"
    NEED_REPAIR=true
fi

if [ "$NGINX_STATUS" != "healthy" ]; then
    log "PERINGATAN: Container nginx tidak sehat (status: $NGINX_STATUS)"
    NEED_REPAIR=true
fi

if [ "$NEED_REPAIR" = true ]; then
    log "REPAIR: Merestart container app, queue, reverb, nginx..."
    docker compose restart app queue reverb nginx

    log "REPAIR: Menunggu 30 detik..."
    sleep 30

    APP_STATUS=$(check_container_health steman_app)
    NGINX_STATUS=$(check_container_health steman_nginx)

    if [ "$APP_STATUS" != "healthy" ] || [ "$NGINX_STATUS" != "healthy" ]; then
        log "REPAIR GAGAL: Melakukan full rebuild..."
        docker compose down
        sleep 5
        docker compose up -d

        sleep 60

        APP_STATUS=$(check_container_health steman_app)
        if [ "$APP_STATUS" != "healthy" ]; then
            log "KRITIS: Full rebuild gagal! Memicu auto-rollback..."
            /usr/local/bin/steman-rollback.sh
        else
            log "SUKSES: Full rebuild berhasil. Sistem sehat kembali."
        fi
    else
        log "SUKSES: Restart berhasil. Sistem sehat kembali."
    fi
else
    HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" "$HEALTH_URL" --max-time 10)
    if [ "$HTTP_STATUS" != "200" ]; then
        log "PERINGATAN: HTTP health check gagal (status: $HTTP_STATUS). Merestart nginx..."
        docker compose restart nginx
        sleep 10
        HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" "$HEALTH_URL" --max-time 10)
        if [ "$HTTP_STATUS" != "200" ]; then
            log "PERINGATAN: Nginx restart gagal. Merestart semua container..."
            docker compose restart
        else
            log "SUKSES: Nginx restart berhasil."
        fi
    else
        log "OK: Semua sistem sehat. HTTP=$HTTP_STATUS"
    fi
fi

log "--- Pengecekan selesai ---"
