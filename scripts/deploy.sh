#!/bin/bash
# Steman Alumni Portal - Deep Master Auto-Deployer
# Usage: ./scripts/deploy.sh

echo "⚡ [1/6] PULLING LATEST SOURCE CODE..."
git fetch --all
git reset --hard origin/main
git clean -e storage/ -fd

echo "📦 [2/6] STOPPING SERVICES GRACEFULLY & SYNCING ENV..."
docker cp /var/www/steman-alumni/.env steman-alumni-app-1:/var/www/.env || true
docker compose -f docker-compose.prod.yml up -d --build

echo "🧹 [3/6] FORCING DEEP CACHE CLEANUP..."
docker exec steman-alumni-app-1 php artisan optimize:clear
docker exec steman-alumni-app-1 php artisan view:clear
docker exec steman-alumni-app-1 php artisan event:clear

echo "🗄️ [4/6] MIGRATING DATABASE..."
docker exec steman-alumni-app-1 php artisan migrate --force

echo "🛡️ [5/6] FIXING STORAGE PERMISSIONS to PREVENT 500 ERRORS..."
docker exec steman-alumni-app-1 chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec steman-alumni-app-1 chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "🚀 [6/6] OPTIMIZING CORE..."
docker exec steman-alumni-app-1 php artisan config:cache
docker exec steman-alumni-app-1 php artisan route:cache || true
docker exec steman-alumni-app-1 php artisan view:cache || true

echo "✅ DEPLOYMENT SUCCESSFUL! PORTAL IS LIVE AND CACHES ARE CLEANSED!"
