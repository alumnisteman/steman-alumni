#!/bin/bash
# ============================================================
# STEMAN ALUMNI - Script Maintenance Otomatis
# Server: 103.175.219.57 | Project: /var/www/steman-alumni
# Jalankan: bash /var/www/steman-alumni/scripts/steman-maintenance.sh
# Tambahkan ke crontab: 0 3 * * * bash /var/www/steman-alumni/scripts/steman-maintenance.sh >> /var/log/steman-maintenance.log 2>&1
# ============================================================

set -e
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')
APP_DIR="/var/www/steman-alumni"
LOG_FILE="/var/log/steman-maintenance.log"
APP_CONTAINER="steman_app"
DB_CONTAINER="steman_db"
DB_USER="app_user"
DB_PASS="strongpassword"
DB_NAME="steman_alumni"

log() { echo "[$TIMESTAMP] $1" | tee -a "$LOG_FILE"; }

log "====== MULAI MAINTENANCE STEMAN ALUMNI ======"

# 1. Bersihkan semua cache Laravel
log "[1/8] Membersihkan Laravel cache..."
docker exec "$APP_CONTAINER" php artisan route:clear 2>/dev/null && log "  route:clear OK" || log "  route:clear SKIP"
docker exec "$APP_CONTAINER" php artisan config:clear 2>/dev/null && log "  config:clear OK" || log "  config:clear SKIP"
docker exec "$APP_CONTAINER" php artisan view:clear 2>/dev/null && log "  view:clear OK" || log "  view:clear SKIP"
docker exec "$APP_CONTAINER" php artisan cache:clear 2>/dev/null && log "  cache:clear OK" || log "  cache:clear SKIP"
docker exec "$APP_CONTAINER" php artisan event:clear 2>/dev/null && log "  event:clear OK" || true

# 2. Rebuild config cache (aman untuk production)
log "[2/8] Rebuild config cache..."
docker exec "$APP_CONTAINER" php artisan config:cache 2>/dev/null && log "  config:cache OK" || log "  config:cache GAGAL"

# 3. Bersihkan file tmp dan upload sementara
log "[3/8] Membersihkan file sementara..."
docker exec "$APP_CONTAINER" find /var/www/storage/framework/views/ -name "*.php" -mtime +7 -delete 2>/dev/null && log "  View cache lama dihapus"
docker exec "$APP_CONTAINER" find /var/www/storage/logs/ -name "*.log" -size +50M -exec truncate -s 10M {} \; 2>/dev/null && log "  Log besar dipotong ke 10MB"

# 4. Optimize tabel database
log "[4/8] Optimize tabel database..."
docker exec "$DB_CONTAINER" mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
OPTIMIZE TABLE users, news, businesses, job_vacancies, activity_logs, sessions, messages, event_themes;
" 2>/dev/null && log "  Database OPTIMIZE OK" || log "  Database OPTIMIZE SKIP"

# 5. Hapus session expired
log "[5/8] Membersihkan session expired..."
docker exec "$DB_CONTAINER" mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(NOW() - INTERVAL 2 DAY);
" 2>/dev/null && log "  Session expired dihapus" || log "  Session cleanup SKIP"

# 6. Hapus cache expired
log "[6/8] Membersihkan cache expired dari DB..."
docker exec "$DB_CONTAINER" mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
DELETE FROM cache WHERE expiration < UNIX_TIMESTAMP(NOW());
DELETE FROM cache_locks WHERE expiration < UNIX_TIMESTAMP(NOW());
" 2>/dev/null && log "  Cache expired dihapus" || log "  Cache cleanup SKIP"

# 7. Jalankan queue restart (supaya queue worker tidak stuck)
log "[7/8] Restart queue worker..."
docker exec "$APP_CONTAINER" php artisan queue:restart 2>/dev/null && log "  Queue restart OK" || log "  Queue restart SKIP"

# 8. Health check akhir
log "[8/8] Health check..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 https://alumni-steman.my.id/ 2>/dev/null || echo "000")
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    log "  Website OK (HTTP $HTTP_CODE)"
else
    log "  WARNING: Website unreachable (HTTP $HTTP_CODE)"
fi

ADMIN_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 https://admin.alumni-steman.my.id/ 2>/dev/null || echo "000")
if [ "$ADMIN_CODE" = "200" ] || [ "$ADMIN_CODE" = "302" ]; then
    log "  Admin panel OK (HTTP $ADMIN_CODE)"
else
    log "  WARNING: Admin panel unreachable (HTTP $ADMIN_CODE)"
fi

log "====== SELESAI MAINTENANCE ($TIMESTAMP) ======"
echo ""
