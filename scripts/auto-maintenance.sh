#!/bin/bash
# Steman Alumni Portal - Deep Housekeeping Bot
# Path: /var/www/steman-alumni/scripts/auto-maintenance.sh

echo "--- STARTING DEEP AUTO-MAINTENANCE [$(date)] ---"

# 1. Docker Cleanup
echo "[1/4] Pruning unused Docker images and build cache..."
docker system prune -f

# 2. Session Housekeeping (Since we use database driver, we clear the physical junk)
echo "[2/4] Clearing legacy physical session files..."
rm -rf /var/www/steman-alumni/storage/framework/sessions/*

# 3. Log Rotation (Cleanup old logs > 7 days)
echo "[3/4] Cleaning Laravel logs older than 7 days..."
find /var/www/steman-alumni/storage/logs -name "*.log" -mtime +7 -delete

# 4. Storage & Framework Optimization
echo "[4/4] Clearing and Re-caching Framework..."
docker exec steman-alumni-app-1 php artisan optimize:clear
docker exec steman-alumni-app-1 php artisan view:clear
docker exec steman-alumni-app-1 php artisan config:cache

echo "--- AUTO-MAINTENANCE COMPLETE [$(date)] ---"
