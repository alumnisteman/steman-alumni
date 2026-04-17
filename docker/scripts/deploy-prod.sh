#!/bin/bash
# ============================================================
# Master Deployment Script - Steman Alumni Portal
# Purpose: Ensures a clean deployment with zero stale cache.
# ============================================================

set -e

PROJECT_DIR="/var/www/steman-alumni"
cd "$PROJECT_DIR"

echo "[$(date)] Starting Master Deployment..."

# 1. Clear Laravel Caches (Before update)
echo "Clearing Laravel caches..."
docker exec steman-alumni-app-1 php artisan optimize:clear || true

# 2. Sync / Update (Optional git pull if using git)
# git pull origin master

# 3. Handle Database
echo "Running database migrations..."
docker exec steman-alumni-app-1 php artisan migrate --force

# 4. Clear Every Cache Layer
echo "Purging all cache layers..."

# A. Application Cache (Redis)
echo "Cleaning Redis..."
docker exec steman_redis redis-cli FLUSHALL > /dev/null

# B. View and Route Cache
docker exec steman-alumni-app-1 php artisan view:clear
docker exec steman-alumni-app-1 php artisan route:clear
docker exec steman-alumni-app-1 php artisan config:clear

# C. Nginx Micro-cache
echo "Cleaning Nginx cache..."
docker exec steman_nginx rm -rf /tmp/nginx_cache/* || true

# D. PHP OPcache
echo "Restarting PHP containers to clear OPcache..."
docker restart steman-alumni-app-1

# 5. Handle Service Worker (The "Sticky" Cache)
# We increment the CACHE_NAME in sw.js or just force a re-registration if needed.
# For now, we manually updated sw.js to v2.

# 6. Final Reboot of Webserver
echo "Restarting Nginx..."
docker restart steman_nginx

echo "[$(date)] Deployment finished successfully!"
echo "Check health: https://alumni-steman.my.id/test-ads"
