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

# Laravel housekeeping (ignore errors for non-critical commands)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Execute the CMD (php-fpm)
exec "$@"
