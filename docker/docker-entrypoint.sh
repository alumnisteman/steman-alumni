#!/bin/sh
set -e

# --- 1. Environment Preparation ---
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# --- 2. Security: Key Generation ---
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# --- 3. Database: Migration ---
MAX_TRIES=30
TRIES=0
until nc -z db 3306 || [ $TRIES -eq $MAX_TRIES ]; do
  echo "Waiting for database (db:3306) to be ready... ($TRIES/$MAX_TRIES)"
  sleep 2
  TRIES=$((TRIES+1))
done

if [ $TRIES -eq $MAX_TRIES ]; then
  echo "Error: Database not reachable after $MAX_TRIES tries."
  exit 1
fi

echo "Running Database Migrations..."
php artisan migrate --force

# --- 4. Performance Tuning (Production) ---
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing Laravel for Production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    # Ensure all storage links are created
    php artisan storage:link || true
fi

# --- 5. Permissions Enforcement ---
echo "Applying runtime permissions..."
chmod -R 755 /var/www 2>/dev/null || true
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# --- 6. Start PHP-FPM or Custom Command ---
echo "Steman Alumni Portal is ready! Executing: $@"
exec "$@"
