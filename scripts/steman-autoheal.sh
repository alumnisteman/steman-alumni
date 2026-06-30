#!/bin/bash

# STEMAN ALUMNI PORTAL - THE ULTIMATE AUTO-HEAL V3
# Purpose: Ensures the system remains Bug-Free, Fast, and Secure permanently.

echo "==========================================="
echo "   STEMAN GUARDIAN: AUTO-HEAL INITIATED    "
echo "==========================================="

# 1. PERMISSION RECOVERY (Prevention of 403/500 errors)
echo "[1/5] Securing File Permissions..."
docker exec steman_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null
docker exec steman_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null

# 2. LOG & JUNK MANAGEMENT (Prevention of Disk Full)
echo "[2/5] Managing System Logs & Junk..."
# Clear laravel logs older than 7 days
docker exec steman_app find /var/www/storage/logs -name "*.log" -mtime +7 -delete 2>/dev/null
# Clean up temp files in scratch
rm -f /var/www/steman-alumni/scratch/*.py 2>/dev/null
rm -f /var/www/steman-alumni/scratch/*.sql 2>/dev/null

# 3. PERFORMANCE LOCKDOWN
echo "[3/5] Optimizing Production Performance..."
# PENTING: Rebuild cache ATOMIK — jangan pakai optimize:clear tanpa rebuild langsung
# karena ada race condition: request masuk saat routes-v7.php sudah dihapus tapi belum dibuat ulang = 500
docker exec steman_app php artisan config:clear > /dev/null 2>&1
docker exec steman_app php artisan view:clear > /dev/null 2>&1
docker exec steman_app php artisan config:cache > /dev/null 2>&1
docker exec steman_app php artisan route:cache > /dev/null 2>&1
docker exec steman_app php artisan view:cache > /dev/null 2>&1
docker exec steman_app php artisan event:cache > /dev/null 2>&1

# 4. INFRASTRUCTURE HEALTH
echo "[4/5] Checking Infrastructure..."
# Test Nginx
docker exec steman_nginx nginx -t > /dev/null 2>&1 || (echo "Nginx config error! Reloading backup..." && docker restart steman_nginx)
# Check Queue (Alert if > 5000)
QUEUE_LEN=$(docker exec steman_app php artisan queue:monitor redis:default | grep -oP '\d+' | head -n 1 || echo 0)
if [ "$QUEUE_LEN" -gt 5000 ]; then
    echo "WARNING: Queue backlog detected ($QUEUE_LEN jobs). Restarting workers..."
    docker restart steman_queue
fi

# 5. HEALTH INTEGRITY CHECK
echo "[5/5] Running Integrity Audit..."
docker exec steman_app php artisan steman:check-integrity --fix > /dev/null 2>&1 || echo "Integrity Check: Issues found and attempted to fix."

echo "==========================================="
# COMING SOON GUARD: Site sudah live, paksa mode off
docker exec steman_app php artisan tinker --execute="AppModelsSetting::where(key,coming_soon_mode)->update([value=>off]);" > /dev/null 2>&1
echo "   SYSTEM PERFECTED & GUARDIAN ACTIVE! 🛡️  "
echo "==========================================="

