#!/bin/bash
# Steman Alumni Portal - Production Deployer
# Usage: ./scripts/deploy.sh
# Dijalankan dari server: /var/www/steman-alumni

set -e

APP_DIR="/var/www/steman-alumni"
APP_CONTAINER="steman_app"
COMPOSE_FILE="docker-compose.prod.yml"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') $1"; }

cd "$APP_DIR" || { log "ERROR: Direktori $APP_DIR tidak ditemukan"; exit 1; }

log "⚡ [1/7] PULL KODE TERBARU..."
git fetch --all
git reset --hard origin/main
git clean -e storage/ -e .env -fd

log "📦 [2/7] BUILD & RESTART CONTAINER (dengan sinkronisasi assets otomatis)..."
docker compose -f "$COMPOSE_FILE" up -d --build --remove-orphans

log "⏳ [3/7] MENUNGGU APP CONTAINER SEHAT (maks 120 detik)..."
WAITED=0
until docker inspect --format='{{.State.Health.Status}}' "$APP_CONTAINER" 2>/dev/null | grep -q "healthy"; do
    if [ $WAITED -ge 120 ]; then
        log "❌ TIMEOUT: Container tidak sehat setelah 120 detik. Cek log dengan: docker logs $APP_CONTAINER"
        docker logs "$APP_CONTAINER" --tail 50
        exit 1
    fi
    sleep 5
    WAITED=$((WAITED + 5))
    log "   ... menunggu ($WAITED/120 detik)"
done
log "✅ Container $APP_CONTAINER sehat."

log "🧹 [4/7] MEMBERSIHKAN CACHE LARAVEL..."
docker exec "$APP_CONTAINER" php artisan optimize:clear || true
docker exec "$APP_CONTAINER" php artisan view:clear || true
docker exec "$APP_CONTAINER" php artisan event:clear || true

log "🗄️  [5/7] MIGRASI DATABASE..."
docker exec "$APP_CONTAINER" php artisan migrate --force

log "🛡️  [6/7] FIX PERMISSION STORAGE..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/storage /var/www/bootstrap/cache

log "🚀 [7/7] OPTIMASI LARAVEL CORE..."
docker exec "$APP_CONTAINER" php artisan config:cache
docker exec "$APP_CONTAINER" php artisan route:cache || true
docker exec "$APP_CONTAINER" php artisan view:cache || true

log "✅ DEPLOY SELESAI! Portal live di https://alumni-steman.my.id"
