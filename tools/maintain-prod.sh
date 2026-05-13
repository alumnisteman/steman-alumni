#!/bin/bash
# STEMAN ALUMNI - PRO-MAINTENANCE SCRIPT (DOCKER VERSION)
# ========================================================
# Version: 2.0
# Fungsi: Sinkronisasi file, bersihkan cache dalam Docker, dan perbaikan izin.

APP_CONTAINER="steman-alumni-app-1"
COMPOSE_FILE="docker-compose.prod.yml"

echo "==========================================="
echo "   STEMAN ALUMNI - AUTO-HEALING SYSTEM     "
echo "==========================================="

# 1. PERBAIKAN VOLUME MOUNT & DOCKER STATE
echo "[1/4] Mengecek Status Sinkronisasi Docker..."
docker inspect $APP_CONTAINER > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "?? Kontainer tidak ditemukan. Mencoba menjalankan ulang..."
    docker compose -f $COMPOSE_FILE up -d
fi

# 2. PERBAIKAN IZIN FILE (HOST & CONTAINER)
echo "[2/4] Merapikan Permission (ID: 82 / www-data)..."
chown -R 82:82 storage bootstrap/cache 2>/dev/null
docker exec $APP_CONTAINER chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
echo "? Permissions OK."

# 3. PEMBERSIHAN CACHE "DEEP FLUSH"
echo "[3/4] Melakukan Deep Flush Cache (Inside Docker)..."
docker exec $APP_CONTAINER php artisan config:clear
docker exec $APP_CONTAINER php artisan route:clear
docker exec $APP_CONTAINER php artisan view:clear
docker exec $APP_CONTAINER php artisan cache:clear

# Hapus manual bootstrap/cache di host untuk keamanan ganda
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/services.php
echo "? System Caches Cleared."

# 4. REFRESH & OPTIMASI (OPSIONAL)
echo "[4/4] Memperbarui State Aplikasi..."
docker exec $APP_CONTAINER php artisan config:cache
docker exec $APP_CONTAINER php artisan view:cache
echo "? Optimasi OK."

echo ""
echo "==========================================="
echo "   SISTEM SEHAT & SINKRON! ????          "
echo "==========================================="
