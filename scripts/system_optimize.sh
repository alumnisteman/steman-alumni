#!/bin/bash

# STEMAN ALUMNI PROD OPTIMIZER V2
# Purpose: Auto-cleanup junk, rotate logs, and clear stale caches.

echo "[$(date)] Starting System Optimization..."

# 1. Clean up temporary deployment artifacts
echo "Cleaning up junk files..."
rm -f /var/www/steman-alumni/*.tar.gz 2>/dev/null
rm -f /var/www/steman-alumni/*.sql 2>/dev/null
rm -f /var/www/steman-alumni/test_*.php 2>/dev/null
rm -f /var/www/steman-alumni/scratch/*.py 2>/dev/null
rm -f /var/www/steman-alumni/scratch/*.sql 2>/dev/null

# 2. Laravel Internals Cleanup
echo "Clearing Laravel caches..."
docker exec app php artisan view:clear > /dev/null 2>&1
docker exec app php artisan cache:clear > /dev/null 2>&1
docker exec app php artisan config:clear > /dev/null 2>&1
docker exec app php artisan route:clear > /dev/null 2>&1

# 3. Log Maintenance
echo "Cleaning old logs (older than 10 days)..."
find /var/www/steman-alumni/storage/logs -name "*.log" -type f -mtime +10 -delete 2>/dev/null

# 4. Storage Optimization
echo "Optimizing storage directory..."
docker exec app php artisan storage:link 2>/dev/null

# 5. Database Optimization
echo "Optimizing database tables..."
# Get all tables and optimize them
TABLES=$(docker exec steman_db mysql -u app_user -pstrongpassword steman_alumni -N -e "SHOW TABLES")
for TABLE in $TABLES; do
    echo "  Optimizing $TABLE..."
    docker exec steman_db mysql -u app_user -pstrongpassword steman_alumni -e "OPTIMIZE TABLE $TABLE" > /dev/null 2>&1
done

# 6. Prune old data
echo "Pruning old records..."
docker exec app php artisan model:prune --path="app/Models" > /dev/null 2>&1

echo "[$(date)] Optimization Complete!"

