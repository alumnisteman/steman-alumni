#!/bin/bash
# Health Check & Auto Recovery Script
# Alumni STEMAN - Cegah error 500 berulang
# Dipanggil oleh cron setiap 5 menit

LOG="/var/log/steman-health.log"
APP_DIR="/var/www/steman-alumni"
DATE=$(date "+%Y-%m-%d %H:%M:%S")

log() { echo "[$DATE] $1" | tee -a "$LOG"; }

# Rotasi log (jaga max 5MB)
if [ -f "$LOG" ] && [ $(stat -c%s "$LOG") -gt 5242880 ]; then
    mv "$LOG" "$LOG.1"
fi

# 1) Cek HTTP response
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 https://alumni-steman.my.id/ 2>/dev/null)

if [ "$HTTP_CODE" = "500" ] || [ "$HTTP_CODE" = "000" ]; then
    log "ALERT: Website error HTTP $HTTP_CODE - memulai recovery..."
    
    cd "$APP_DIR"
    
    # Clear semua cache Laravel
    docker exec steman_app php artisan cache:clear 2>/dev/null
    docker exec steman_app php artisan view:clear 2>/dev/null
    docker exec steman_app php artisan config:clear 2>/dev/null
    
    # Restart containers
    docker restart steman_app
    sleep 15
    
    # Cek lagi
    HTTP_CODE2=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 https://alumni-steman.my.id/ 2>/dev/null)
    if [ "$HTTP_CODE2" = "200" ]; then
        log "RECOVERY: Berhasil! Website kembali normal (HTTP 200)"
    else
        log "CRITICAL: Recovery gagal! HTTP masih $HTTP_CODE2 - perlu tindakan manual"
    fi
elif [ "$HTTP_CODE" = "200" ]; then
    log "OK: Website sehat (HTTP 200)"
else
    log "WARN: HTTP code tidak terduga: $HTTP_CODE"
fi

# 2) Cek container berjalan
for CONTAINER in steman_app steman_nginx steman_db steman_redis; do
    STATUS=$(docker inspect --format="{{.State.Status}}" "$CONTAINER" 2>/dev/null)
    if [ "$STATUS" != "running" ]; then
        log "ALERT: Container $CONTAINER tidak running (status: $STATUS) - restart..."
        docker start "$CONTAINER" 2>/dev/null
        log "Container $CONTAINER direstart"
    fi
done
