#!/bin/bash
# Script Instalasi Otomatis STEMAN Alumni v5 (CentOS 8/9, RHEL, AlmaLinux)
# Mendukung: Social Login, Reverb (WebSocket Real-time), PWA, Analytics

echo "====================================================="
echo "   STEMAN Alumni v5 - Auto Installer"
echo "   Sistem Operasi: CentOS/RHEL/AlmaLinux"
echo "====================================================="

# 1. Update sistem dan tambahkan repository EPEL & Remi
echo "[1/7] Memperbarui sistem dan repositori..."
sudo dnf install -y epel-release
sudo dnf install -y "https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E %rhel).rpm" || true
sudo dnf install -y curl zip unzip git nano

# 2. Install Nginx, MariaDB, dan PHP 8.2 + sockets
echo "[2/7] Menginstal Nginx, MariaDB, PHP 8.2, dan ekstensi sockets..."
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.2 -y
sudo dnf install -y nginx mariadb-server \
    php-fpm php-mysqlnd php-mbstring php-xml \
    php-bcmath php-curl php-zip php-gd php-intl \
    php-sockets
# CATATAN: php-sockets WAJIB untuk Laravel Reverb (WebSocket real-time)

sudo systemctl enable --now nginx mariadb php-fpm

# 3. Install Node.js 20 LTS & npm (untuk build frontend Vite/PWA)
echo "[3/7] Menginstal Node.js 20 LTS dan npm..."
curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
sudo dnf install -y nodejs

# 4. Install Composer
echo "[4/7] Menginstal Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# 5. Setup Database MariaDB
echo "[5/7] Menyiapkan Database MariaDB..."
read -p "Masukkan nama database (contoh: steman_alumni): " db_name
read -p "Masukkan username database: " db_user
read -sp "Masukkan password database: " db_pass
echo ""

sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`${db_name}\`;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${db_user}'@'localhost';"
sudo mysql -e "ALTER USER '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`${db_name}\`.* TO '${db_user}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 6. Konfigurasi Aplikasi
echo "[6/7] Mengonfigurasi Aplikasi Laravel..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

sed -i "s|DB_DATABASE=.*|DB_DATABASE=${db_name}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${db_user}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${db_pass}|" .env
sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env

composer install --optimize-autoloader --no-dev --no-interaction
npm install --silent
npm run build

php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Mengatur Hak Akses (Permissions) & SELinux
echo "[7/7] Mengatur Hak Akses & SELinux..."
APP_DIR=$(pwd)
WEB_USER="nginx"
if ! id "$WEB_USER" &>/dev/null; then WEB_USER="apache"; fi

sudo chown -R $WEB_USER:$WEB_USER "$APP_DIR"
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

if command -v semanage &> /dev/null; then
    echo "Mengatur SELinux context..."
    sudo chcon -Rt httpd_sys_content_t "$APP_DIR" 2>/dev/null || true
    sudo chcon -Rt httpd_sys_rw_content_t "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
    sudo setsebool -P httpd_can_network_connect_db 1 2>/dev/null || true
fi

# Setup Queue Worker sebagai systemd service
# (WAJIB untuk notifikasi real-time & antrian email)
echo "Menyiapkan Queue Worker (systemd service)..."
sudo tee /etc/systemd/system/steman-queue.service > /dev/null <<EOF
[Unit]
Description=STEMAN Alumni Queue Worker
After=network.target

[Service]
User=${WEB_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan queue:work --tries=3 --timeout=60 --sleep=3
Restart=on-failure
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF
sudo systemctl daemon-reload
sudo systemctl enable --now steman-queue.service

echo "====================================================="
echo " Instalasi Berhasil Selesai! (STEMAN Alumni v5)"
echo ""
echo " LANGKAH PASCA-INSTALASI:"
echo " 1. Konfigurasi Nginx dan arahkan document root ke:"
echo "    $APP_DIR/public"
echo " 2. Edit .env dan isi kredensial Social Login:"
echo "    GOOGLE_CLIENT_ID=..."
echo "    GOOGLE_CLIENT_SECRET=..."
echo "    GITHUB_CLIENT_ID=..."
echo "    GITHUB_CLIENT_SECRET=..."
echo " 3. Akun Admin Default:"
echo "    Email : admin@steman.ac.id"
echo "    Pass  : Admin@1234"
echo "    Segera ganti password setelah login pertama!"
echo "====================================================="
