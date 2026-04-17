#!/bin/bash
# ============================================================
# Master Maintenance Script - Steman Alumni Portal
# Versi: 2.0 | Diupdate: April 2026
# ============================================================

PROJECT_DIR="/var/www/steman-alumni"
LOG_DIR="$PROJECT_DIR/storage/logs"
MAX_LOG_SIZE=50000 # 50MB in KB
MAINTENANCE_LOG="$PROJECT_DIR/backups/maintenance.log"
HEALTH_REPORT="$PROJECT_DIR/backups/health_report.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$MAINTENANCE_LOG"
}

report() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$HEALTH_REPORT"
}

mkdir -p "$PROJECT_DIR/backups"
touch "$MAINTENANCE_LOG"
echo "--- HEALTH REPORT $(date) ---" > "$HEALTH_REPORT"

log "--- MEMULAI MAINTENANCE RUTIN v2.0 ---"

# 1. Proactive Heartbeat Checks
log "Checking core services heartbeat..."
if docker exec steman_db mysqladmin ping -h localhost -u root --password="$(grep '^DB_PASSWORD=' $PROJECT_DIR/.env | cut -d '=' -f2)" > /dev/null 2>&1; then
    report "DATABASE: Healthy"
else
    report "DATABASE: UNHEALTHY / Unreachable"
fi

if docker exec steman_redis redis-cli ping | grep -q "PONG"; then
    report "REDIS: Healthy"
else
    report "REDIS: UNHEALTHY / Unreachable"
fi

# 2. Log Analysis
log "Analyzing recent application logs for errors..."
ERRORS=$(tail -n 1000 "$LOG_DIR/laravel.log" 2>/dev/null | grep -icE "error|fatal|exception")
report "APP_ERRORS_LAST_1000_LINES: $ERRORS"

# 3. Docker Housekeeping
log "Cleaning up unused Docker resources..."
docker system prune -f --volumes > /dev/null
log "Docker cleanup selesai."

# 4. Laravel Housekeeping
log "Clearing Laravel caches and views..."
docker exec steman-alumni-app-1 php artisan optimize:clear > /dev/null
docker exec steman-alumni-app-1 php artisan view:clear > /dev/null
log "Laravel cache cleared."

# 5. Advanced Log Rotation (Zipping)
log "Performing advanced log rotation..."
for logfile in $LOG_DIR/*.log; do
    if [ -f "$logfile" ]; then
        filesize=$(du -k "$logfile" | cut -f1)
        if [ "$filesize" -gt "$MAX_LOG_SIZE" ]; then
            log "Archiving large log file: $(basename $logfile) (${filesize}KB)"
            ZIP_NAME="$LOG_DIR/archives/$(basename $logfile)-$(date +%Y%m%d%H%M).gz"
            mkdir -p "$LOG_DIR/archives"
            gzip -c "$logfile" > "$ZIP_NAME"
            echo "" > "$logfile" # Truncate after zipping
        fi
    fi
done

# 6. Proactive Fixes (Garbage & Links)
log "Cleaning up stale sessions and ensuring storage links..."
find "$PROJECT_DIR/storage/framework/sessions" -type f -mtime +1 -delete
docker exec steman-alumni-app-1 php artisan storage:link > /dev/null 2>&1
log "Cleanup and links verified."

# 7. Permission Guard
log "Applying permission reinforcement..."
chown -R www-data:www-data "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache" 2>/dev/null
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache" 2>/dev/null
log "Permissions enforced."

log "Maintenance v3.0 selesai."
log "---------------------------------"
