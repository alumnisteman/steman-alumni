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

# Run migrations once if enabled
if [ "${RUN_MIGRATIONS}" = "true" ]; then
  php artisan migrate --force
fi

# Laravel housekeeping — cache untuk performa, fallback ke clear jika gagal
php artisan config:cache  || php artisan config:clear  || true
php artisan cache:clear                                || true
php artisan route:cache   || php artisan route:clear   || true
php artisan view:cache    || php artisan view:clear    || true

# ─── Laravel Scheduler via crond ────────────────────────────────────
# Buat file crontab untuk www-data agar Laravel Scheduler berjalan setiap menit.
# Menggunakan busybox crond yang sudah tersedia di Alpine Linux.
CRON_FILE="/etc/crontabs/www-data"
mkdir -p /etc/crontabs
echo "* * * * * cd /var/www && php artisan schedule:run >> /var/www/storage/logs/scheduler.log 2>&1" > "${CRON_FILE}"
chmod 600 "${CRON_FILE}"

# Pastikan direktori log ada dan bisa ditulis
mkdir -p /var/www/storage/logs
chown -R www-data:www-data /var/www/storage/logs 2>/dev/null || true

# Jalankan crond di background (busybox crond, Alpine)
crond -b -l 8 -L /var/www/storage/logs/crond.log 2>/dev/null || \
  crond -b 2>/dev/null || \
  echo "[WARN] crond tidak bisa dijalankan, scheduler mungkin tidak aktif"

echo "[OK] Laravel Scheduler cron aktif"
# ────────────────────────────────────────────────────────────────────

# Execute the CMD (php-fpm)
exec "$@"
