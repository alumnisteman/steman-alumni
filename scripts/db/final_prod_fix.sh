#!/bin/bash
# Steman Alumni Production Fix Script

# 1. Configuration
PROJECT_DIR="/var/www/steman-alumni"
ROOT_DB_PASS="rootpassword123"

# 2. Extract Credentials from .env
if [ ! -f "$PROJECT_DIR/.env" ]; then
    echo "ERROR: .env file not found at $PROJECT_DIR"
    exit 1
fi

DB_USER=$(grep DB_USERNAME "$PROJECT_DIR/.env" | cut -d '=' -f 2 | tr -d '\r')
DB_PASS=$(grep DB_PASSWORD "$PROJECT_DIR/.env" | cut -d '=' -f 2 | tr -d '\r')
DB_NAME=$(grep DB_DATABASE "$PROJECT_DIR/.env" | cut -d '=' -f 2 | tr -d '\r')

echo "Database Settings from .env: $DB_USER @ $DB_NAME"

# 3. Sync MariaDB User and Privileges
echo "--- Syncing Database User ---"
docker exec steman_db mysql -u root -p"$ROOT_DB_PASS" -e "
DROP USER IF EXISTS '$DB_USER'@'%';
CREATE USER '$DB_USER'@'%' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';
FLUSH PRIVILEGES;
"

# 4. Clear Laravel Caches on All Replicas
echo "--- Purging Laravel Caches ---"
for i in 1 2 3; do
    CONTAINER="steman-alumni-app-$i"
    if [ $(docker ps -q -f name="$CONTAINER") ]; then
        echo "Clearing cache on $CONTAINER..."
        docker exec "$CONTAINER" php artisan config:clear
        docker exec "$CONTAINER" php artisan view:clear
        docker exec "$CONTAINER" php artisan route:clear
        docker exec "$CONTAINER" php artisan cache:clear
    else
        echo "Skipping $CONTAINER (not running)"
    fi
done

# 5. Full Restart of the App
echo "--- Restarting Production Stack ---"
cd "$PROJECT_DIR"
docker compose -f docker-compose.prod.yml restart app

echo "=== FIX COMPLETE ==="
echo "Please refresh https://103.175.219.57/global-network"
