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

# --- 6. Laravel Scheduler (Cron) ---
echo "Setting up Laravel Scheduler..."
echo "* * * * * cd /var/www && php artisan schedule:run >> /var/www/storage/logs/scheduler.log 2>&1" | crontab -
crond -b -l 8
echo "Scheduler cron is active."

# --- 7. Meilisearch: Auto-Configure & Re-Index (Resilient) ---
MEILI_HOST="${MEILISEARCH_HOST:-http://steman_meilisearch:7700}"
MEILI_KEY="${MEILISEARCH_KEY:-stemanMasterKey123}"

echo "Waiting for Meilisearch at $MEILI_HOST ..."
MEILI_TRIES=0
MEILI_MAX=20
until wget -q --spider "${MEILI_HOST}/health" 2>/dev/null || [ $MEILI_TRIES -eq $MEILI_MAX ]; do
    sleep 2
    MEILI_TRIES=$((MEILI_TRIES+1))
done

if [ $MEILI_TRIES -lt $MEILI_MAX ]; then
    echo "Meilisearch is ready. Configuring index settings (geo-sort)..."
    # Configure sortable & filterable attributes for geo radar feature
    wget -q -O /dev/null \
        --method=PATCH \
        --header="Content-Type: application/json" \
        --header="Authorization: Bearer ${MEILI_KEY}" \
        --body-data='{"sortableAttributes":["_geo"],"filterableAttributes":["major","graduation_year","id"]}' \
        "${MEILI_HOST}/indexes/users/settings" || true
    echo "Meilisearch configured. Importing user index..."
    php artisan scout:import "App\Models\User" >> /var/www/storage/logs/scheduler.log 2>&1 &
    echo "Scout import running in background."
else
    echo "WARNING: Meilisearch not reachable within timeout. App will use Eloquent fallback."
fi

# --- 8. Start PHP-FPM or Custom Command ---
echo "Steman Alumni Portal is ready! Executing: $@"
exec "$@"
