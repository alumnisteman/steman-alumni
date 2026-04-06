#!/bin/bash
set -e

echo "=== [1/5] Login ke MariaDB via unix socket (tanpa password) ==="
# MariaDB di container bisa diakses sebagai root OS melalui unix_socket plugin
docker exec steman_db mariadb --user=root --skip-password -e "SELECT 'Connected as root via socket!' as Status;" 2>&1

echo ""
echo "=== [2/5] Melihat semua user yang ada ==="
docker exec steman_db mariadb --user=root --skip-password -e "SELECT User, Host, plugin FROM mysql.user;" 2>&1

echo ""
echo "=== [3/5] Memperbaiki app_user agar bisa connect dari manapun ==="
docker exec steman_db mariadb --user=root --skip-password -e "
DROP USER IF EXISTS 'app_user'@'%';
CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'strongpassword';
GRANT ALL PRIVILEGES ON steman_alumni.* TO 'app_user'@'%';
FLUSH PRIVILEGES;
SELECT 'app_user fixed!' as Status;
" 2>&1

echo ""
echo "=== [4/5] Set root password untuk masa depan ==="
docker exec steman_db mariadb --user=root --skip-password -e "
ALTER USER 'root'@'localhost' IDENTIFIED BY 'rootpassword123';
FLUSH PRIVILEGES;
" 2>&1
echo "Root password set to: rootpassword123"

echo ""
echo "=== [5/5] Test koneksi app_user ==="
docker exec steman_db mariadb -u app_user -pstrongpassword steman_alumni -e "SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema='steman_alumni';" 2>&1

echo ""
echo "=== Restart app containers ==="
cd /var/www/steman-alumni
docker compose -f docker-compose.prod.yml restart
sleep 20

echo ""
echo "=== Status akhir container ==="
docker ps -a --format "table {{.Names}}\t{{.Status}}"
