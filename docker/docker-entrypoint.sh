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

# Laravel housekeeping
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan key:generate --force

# Execute the CMD (php-fpm)
exec "$@"
