#!/bin/bash

# Steman Alumni - Universal Maintenance & Self-Healing Script
# Version: 1.0
# Purpose: Fix permissions, clear caches, and refresh application state.

echo "==========================================="
echo "   STEMAN ALUMNI - MAINTENANCE MODE ??   "
echo "==========================================="

# 1. PERMISSIONS
echo "[1/4] Merapikan Permission Storage & Cache..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo "? Permissions OK."

# 2. LARAVEL CACHE CLEANUP
echo "[2/4] Membersihkan Cache Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo "? Cache Terhapus."

# 3. COMPOSER AUTOLOAD
echo "[3/4] Optimasi Autoload & Config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "? Optimasi OK."

# 4. HEALTH CHECK
echo "[4/4] Verifikasi Koneksi Database..."
php artisan db:show > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "? Database Terhubung."
else
    echo "? KONEKSI DATABASE BERMASALAH!"
fi

echo ""
echo "==========================================="
echo "   SISTEM TELAH PULIH (HEALED)! ???       "
echo "==========================================="
