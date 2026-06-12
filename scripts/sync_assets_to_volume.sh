#!/bin/bash
# =============================================================================
# sync_assets_to_volume.sh
# Sinkronisasi assets (CSS/JS/build) dari disk ke Docker volume app_public
# Jalankan setelah setiap deploy untuk memastikan assets selalu tersedia.
# Usage: ./scripts/sync_assets_to_volume.sh
# =============================================================================

APP_CONTAINER="steman_app"
APP_DIR="/var/www/steman-alumni"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') [SYNC] $1"; }

log "Memulai sinkronisasi assets ke Docker volume..."

# Pastikan container sedang running
if ! docker ps --format '{{.Names}}' | grep -q "^${APP_CONTAINER}$"; then
    log "ERROR: Container $APP_CONTAINER tidak berjalan. Jalankan docker compose up terlebih dahulu."
    exit 1
fi

# Salin seluruh public/ dari host ke dalam container (volume app_public)
log "Menyalin public/build dari host ke container..."
docker cp "${APP_DIR}/public/build" "${APP_CONTAINER}:/var/www/public/build"

# Salin file-file root public (index.php, robots.txt, dll)
log "Menyalin file root public..."
docker cp "${APP_DIR}/public/index.php" "${APP_CONTAINER}:/var/www/public/index.php" || true
docker cp "${APP_DIR}/public/robots.txt" "${APP_CONTAINER}:/var/www/public/robots.txt" || true
docker cp "${APP_DIR}/public/.htaccess" "${APP_CONTAINER}:/var/www/public/.htaccess" || true
docker cp "${APP_DIR}/public/favicon.ico" "${APP_CONTAINER}:/var/www/public/favicon.ico" || true

# Perbaiki permission
log "Memperbaiki permission..."
docker exec "${APP_CONTAINER}" chown -R www-data:www-data /var/www/public
docker exec "${APP_CONTAINER}" chmod -R 755 /var/www/public

log "Sinkronisasi assets selesai!"
log "CSS/JS tersedia di: https://alumni-steman.my.id"
