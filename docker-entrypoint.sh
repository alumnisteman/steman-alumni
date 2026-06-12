#!/bin/sh
set -e

# Wait for DB and Redis services to be ready
while ! nc -z db 3306; do
  echo "Waiting for MariaDB..."
  sleep 2
done
while ! nc -z redis 6379; do
  echo "Waiting for Redis..."
  sleep 2
done

# -----------------------------------------------------------------------
# SINKRONISASI ASSETS: Pastikan public/build tersedia di Docker volume
# Volume app_public di-mount ke /var/www/public sehingga menimpa file
# dari image. Snapshot di /var/www/public_snapshot/ selalu berisi
# file terbaru dari image dan di-copy ke volume saat container start.
# -----------------------------------------------------------------------
if [ -d "/var/www/public_snapshot" ]; then
    echo ">>> [ASSETS] Menyinkronkan assets ke volume public..."

    # Copy semua file yang belum ada di volume (tanpa overwrite)
    cp -rn /var/www/public_snapshot/. /var/www/public/ 2>/dev/null || true

    # Paksa overwrite direktori build/ agar selalu pakai versi terbaru dari image
    if [ -d "/var/www/public_snapshot/build" ]; then
        echo ">>> [ASSETS] Memperbarui direktori build/ dari snapshot..."
        rm -rf /var/www/public/build
        cp -r /var/www/public_snapshot/build /var/www/public/build
    fi

    # Pastikan index.php root selalu ada
    if [ -f "/var/www/public_snapshot/index.php" ]; then
        cp /var/www/public_snapshot/index.php /var/www/public/index.php
    fi

    chown -R www-data:www-data /var/www/public 2>/dev/null || true
    chmod -R 755 /var/www/public 2>/dev/null || true
    echo ">>> [ASSETS] Sinkronisasi selesai."
else
    echo ">>> [ASSETS] WARNING: /var/www/public_snapshot tidak ditemukan, lewati sinkronisasi."
fi

# Run migrations once if enabled
if [ "${RUN_MIGRATIONS}" = "true" ]; then
  php artisan migrate --force
fi

# Laravel housekeeping (ignore errors for non-critical commands)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Execute the CMD (php-fpm)
exec "$@"
