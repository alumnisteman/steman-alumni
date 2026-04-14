#!/bin/bash
# Steman Alumni Portal - Auto-Maintenance Bot
# Path: /var/www/steman-alumni/scripts/auto-maintenance.sh

echo "--- STARTING AUTO-MAINTENANCE [$(date)] ---"

# 1. Docker Cleanup
echo "[1/3] Pruning unused Docker images and build cache..."
docker system prune -f

# 2. Log Rotation (Cleanup old logs > 7 days)
echo "[2/3] Cleaning Laravel logs older than 7 days..."
find /var/www/steman-alumni/storage/logs -name "*.log" -mtime +7 -delete

# 3. Storage Optimization
echo "[3/3] Clearing temporary framework caches..."
docker exec steman-alumni-app-1 php artisan optimize:clear
docker exec steman-alumni-app-1 php artisan view:clear

echo "--- AUTO-MAINTENANCE COMPLETE [$(date)] ---"
