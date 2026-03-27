#!/bin/bash
# Script Instalasi Otomatis STEMAN Alumni v5 (Ubuntu 20.04/22.04/24.04)
# Mendukung: Social Login, Reverb (WebSocket Real-time), PWA, Analytics

echo "====================================================="
echo "   STEMAN Alumni v5 - Auto Installer"
echo "   Sistem Operasi: Ubuntu/Debian"
echo "====================================================="

# 1. Update sistem dan install dependencies
echo "[1/7] Memperbarui sistem dan menginstal dependensi dasar..."
sudo apt-get update -y
sudo apt-get install -y software-properties-common curl zip unzip git

# Tambah PPA Ondrej untuk PHP 8.2 (Laravel 12 butuh PHP 8.2+)
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update -y

echo "[2/7] Menginstal Nginx, MySQL, PHP 8.2, dan ekstensi sockets..."
sudo apt-get install -y nginx mysql-server \
    php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
    php8.2-intl php8.2-sockets
# CATATAN: php8.2-sockets WAJIB untuk Laravel Reverb (WebSocket real-time)

sudo systemctl enable --now nginx mysql php8.2-fpm

# 2. Install Node.js 20 LTS & npm (untuk build frontend Vite/PWA)
echo "[3/7] Menginstal Node.js 20 LTS dan npm..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# 3. Install Composer
echo "[4/7] Menginstal Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# 4. Setup Database MySQL
echo "[5/7] Menyiapkan Database MySQL..."
read -p "Masukkan nama database (contoh: steman_alumni): " db_name
read -p "Masukkan username database: " db_user
read -sp "Masukkan password database: " db_pass
echo ""

sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`${db_name}\`;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${db_user}'@'localhost';"
sudo mysql -e "ALTER USER '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`${db_name}\`.* TO '${db_user}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 5. Konfigurasi Aplikasi
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

# 6. Mengatur Hak Akses
echo "[7/7] Mengatur Hak Akses Direktori..."
APP_DIR=$(pwd)
sudo chown -R www-data:www-data "$APP_DIR"
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chgrp -R www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R ug+rwx "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

# Setup Queue Worker sebagai systemd service
# (WAJIB untuk notifikasi real-time & antrian email)
echo "Menyiapkan Queue Worker (systemd service)..."
sudo tee /etc/systemd/system/steman-queue.service > /dev/null <<EOF
[Unit]
Description=STEMAN Alumni Queue Worker
After=network.target

[Service]
User=www-data
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
