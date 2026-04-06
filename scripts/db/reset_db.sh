#!/bin/bash
# Script: reset MariaDB root access and fix app_user grants
# Run as: bash /tmp/reset_db.sh on the server

set -e
echo "=========================================="
echo " STEMAN DB RESET SCRIPT"
echo "=========================================="

# Step 1: Stop the app containers (not db)
echo "[1/6] Stopping app containers..."
cd /var/www/steman-alumni
docker stop steman-alumni-app-1 steman-alumni-app-2 steman-alumni-app-3 steman_nginx 2>/dev/null || true

# Step 2: Start a temporary MariaDB with skip-grant-tables
echo "[2/6] Starting MariaDB in skip-grant mode..."
docker stop steman_db 2>/dev/null || true
sleep 3

# Run a temp mariadb with skip-grant-tables on the SAME dbdata volume
docker run --rm -d \
  --name mariadb_recovery \
  --network steman-alumni_steman-network \
  -v steman-alumni_dbdata:/var/lib/mysql \
  -e MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1 \
  mariadb:10.6 --skip-grant-tables --skip-networking=false

echo "Waiting 15s for recovery DB to start..."
sleep 15

# Step 3: Fix the users
echo "[3/6] Fixing user grants..."
docker exec mariadb_recovery mariadb -u root -e "
FLUSH PRIVILEGES;
DROP USER IF EXISTS 'app_user'@'%';
CREATE USER 'app_user'@'%' IDENTIFIED BY 'strongpassword';
GRANT ALL PRIVILEGES ON steman_alumni.* TO 'app_user'@'%';
ALTER USER 'root'@'localhost' IDENTIFIED BY 'rootpassword123';
FLUSH PRIVILEGES;
SELECT User, Host FROM mysql.user;
"

echo "[4/6] Stopping recovery container..."
docker stop mariadb_recovery
sleep 5

# Step 5: Start normal db
echo "[5/6] Starting normal MariaDB..."
cd /var/www/steman-alumni
docker compose -f docker-compose.prod.yml up -d db
echo "Waiting 10s..."
sleep 10

# Step 6: Test connection and restart all
echo "[6/6] Testing connection & restarting all..."
docker exec steman_db mariadb -u app_user -pstrongpassword steman_alumni \
  -e "SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema='steman_alumni';" 2>&1 \
  && echo "=== DB CONNECTION OK ===" \
  || echo "=== DB CONNECTION FAILED ==="

docker compose -f docker-compose.prod.yml up -d
sleep 20

echo ""
echo "=== Final container status ==="
docker ps -a --format "table {{.Names}}\t{{.Status}}"
