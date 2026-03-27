#!/bin/bash
# =============================================================================
#   STEMAN Alumni - Script Instalasi ALL-IN-ONE v5
#   Mendukung: Ubuntu 20.04/22.04/24.04 | CentOS 8/9 | RHEL | AlmaLinux
#   Termasuk: Nginx, PHP 8.2+sockets, MySQL/MariaDB, Node.js, Composer, phpMyAdmin
#   Fitur v5: Social Login, Real-time (Reverb), PWA, Analytics
#   Mendukung Re-install (Menimpa file & DB lama tanpa error)
# =============================================================================

set -e

# --- Warna Terminal ---
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

print_header() {
    echo ""
    echo -e "${CYAN}${BOLD}=============================================${NC}"
    echo -e "${CYAN}${BOLD}  $1${NC}"
    echo -e "${CYAN}${BOLD}=============================================${NC}"
    echo ""
}
print_step()  { echo -e "${GREEN}[✔] $1${NC}"; }
print_info()  { echo -e "${YELLOW}[→] $1${NC}"; }
print_error() { echo -e "${RED}[✘] ERROR: $1${NC}"; exit 1; }

# =============================================================================
# PENGECEKAN AWAL
# =============================================================================
print_header "STEMAN Alumni - Installer v5 (Aman untuk Re-install)"

if [ ! -f "artisan" ]; then
    print_error "Jalankan script dari dalam direktori aplikasi STEMAN Alumni (yang ada file 'artisan').\n  Contoh: cd /var/www/steman-alumni && bash install.sh"
fi

APP_DIR=$(pwd)
print_info "Direktori Aplikasi : $APP_DIR"

# =============================================================================
# STEP 1: DETEKSI OS
# =============================================================================
print_header "STEP 1/8 - Mendeteksi Sistem Operasi"

if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_ID=$ID
    OS_VER=$VERSION_ID
else
    print_error "Tidak dapat mendeteksi sistem operasi."
fi

IS_UBUNTU=false; IS_CENTOS=false

case "$OS_ID" in
    ubuntu|debian|linuxmint)
        IS_UBUNTU=true
        print_step "Terdeteksi: Ubuntu/Debian ($OS_ID $OS_VER)"
        ;;
    centos|rhel|almalinux|rocky|fedora)
        IS_CENTOS=true
        print_step "Terdeteksi: CentOS/RHEL/AlmaLinux ($OS_ID $OS_VER)"
        ;;
    *)
        print_error "OS tidak didukung: $OS_ID"
        ;;
esac

# =============================================================================
# STEP 2: INSTALASI DEPENDENSI
# =============================================================================
print_header "STEP 2/8 - Instalasi Nginx, PHP 8.2 + sockets, MySQL, Node.js, Composer"

if $IS_UBUNTU; then
    print_info "Update repositori..."
    sudo apt-get update -y -q
    sudo apt-get install -y -q software-properties-common curl zip unzip git wget

    sudo add-apt-repository -y ppa:ondrej/php
    sudo apt-get update -y -q

    print_info "Install Nginx, MySQL, PHP 8.2 + sockets..."
    sudo apt-get install -y -q nginx mysql-server \
        php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
        php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
        php8.2-sockets
    # php8.2-sockets WAJIB untuk Laravel Reverb (WebSocket real-time)

    print_info "Install Node.js 20 LTS (untuk build frontend Vite/PWA)..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash - -y
    sudo apt-get install -y -q nodejs

    sudo systemctl enable --now nginx mysql php8.2-fpm

    PHP_SOCK=$(find /var/run/php/ -name "php8.2-fpm.sock" 2>/dev/null | head -n 1)
    [ -z "$PHP_SOCK" ] && PHP_SOCK="/var/run/php/php8.2-fpm.sock"
    WEB_USER="www-data"

elif $IS_CENTOS; then
    print_info "Update repositori..."
    sudo dnf install -y epel-release
    sudo dnf install -y "https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E %rhel).rpm" || true
    sudo dnf install -y curl zip unzip git wget

    print_info "Install Nginx, MariaDB, PHP 8.2 + sockets..."
    sudo dnf module reset php -y
    sudo dnf module enable php:remi-8.2 -y
    sudo dnf install -y nginx mariadb-server \
        php-fpm php-mysqlnd php-mbstring php-xml \
        php-bcmath php-curl php-zip php-gd php-intl \
        php-sockets
    # php-sockets WAJIB untuk Laravel Reverb (WebSocket real-time)

    print_info "Install Node.js 20 LTS (untuk build frontend Vite/PWA)..."
    curl -fsSL https://rpm.nodesource.com/setup_20.x | sudo bash -
    sudo dnf install -y nodejs

    sudo systemctl enable --now nginx mariadb php-fpm

    PHP_SOCK=$(find /var/run/php-fpm/ -name "*.sock" 2>/dev/null | head -n 1)
    [ -z "$PHP_SOCK" ] && PHP_SOCK="/var/run/php-fpm/www.sock"
    WEB_USER="nginx"
fi

# Install Composer
print_info "Cek / install Composer..."
if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi
print_step "Composer siap."

# =============================================================================
# STEP 3: SETUP DATABASE
# =============================================================================
print_header "STEP 3/8 - Konfigurasi Database"

echo -e "${YELLOW}Masukkan informasi database aplikasi (menimpa jika sudah ada):${NC}"
read -p "  Nama database      (contoh: steman_alumni) : " db_name
read -p "  Username database  (contoh: steman_user)   : " db_user
read -sp "  Password database                          : " db_pass
echo ""

print_info "Membuat/memperbarui database dan user MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`${db_name}\`;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${db_user}'@'localhost';"
sudo mysql -e "ALTER USER '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON \`${db_name}\`.* TO '${db_user}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
print_step "Database '${db_name}' dan user '${db_user}' siap digunakan."

# =============================================================================
# STEP 4: KONFIGURASI LARAVEL (.env)
# =============================================================================
print_header "STEP 4/8 - Konfigurasi Aplikasi Laravel"

read -p "Masukkan Domain / IP Server (contoh: alumni.smkn2.sch.id atau 192.168.1.10): " app_domain
APP_URL="http://${app_domain}"

print_info "Membuat ulang file .env (menimpa file lama agar bersih)..."
cp -f .env.example .env

sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|"           .env
sed -i "s|APP_ENV=.*|APP_ENV=production|"            .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|"              .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${db_name}|"    .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${db_user}|"    .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${db_pass}|"    .env
print_step ".env berhasil dikonfigurasi."

# =============================================================================
# STEP 5: COMPOSER & ARTISAN
# =============================================================================
print_header "STEP 5/8 - Instalasi Dependensi & Migrasi Database"

print_info "Install dependensi Composer..."
composer install --optimize-autoloader --no-dev --no-interaction

print_info "Generate Application Key..."
php artisan key:generate --force

print_info "Jalankan migrasi database ulang (menimpa tabel/data lama)..."
php artisan migrate:fresh --seed --force

print_info "Symlink storage..."
php artisan storage:link || true # Abaikan pesan error jika link sudah ada

print_info "Build frontend assets (Vite/PWA)..."
npm install --silent
npm run build

print_info "Optimasi cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_step "Laravel siap!"

# =============================================================================
# STEP 6: HAK AKSES FILE (Anti 403 Forbidden)
# =============================================================================
print_header "STEP 6/8 - Mengatur Hak Akses Direktori"

sudo chown -R $WEB_USER:$WEB_USER "$APP_DIR"
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chgrp -R $WEB_USER "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R ug+rwx "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

if $IS_CENTOS && command -v semanage &>/dev/null; then
    sudo chcon -Rt httpd_sys_content_t "$APP_DIR" 2>/dev/null || true
    sudo chcon -Rt httpd_sys_rw_content_t "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
    sudo setsebool -P httpd_can_network_connect_db 1 2>/dev/null || true
fi
print_step "Hak akses berhasil diatur."

# =============================================================================
# STEP TAMBAHAN v5: QUEUE WORKER (untuk notifikasi & Reverb)
# =============================================================================
print_header "STEP TAMBAHAN v5 - Setup Queue Worker (Real-time Notifications)"

print_info "Membuat systemd service untuk Queue Worker..."
sudo tee /etc/systemd/system/steman-queue.service > /dev/null <<EOF
[Unit]
Description=STEMAN Alumni Queue Worker
After=network.target

[Service]
User=${WEB_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan queue:work --tries=3 --sleep=3 --timeout=60
Restart=on-failure
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF
sudo systemctl daemon-reload
sudo systemctl enable --now steman-queue.service
print_step "Queue Worker berhasil diaktifkan sebagai service."

# =============================================================================
# STEP 7: INSTALL & KONFIGURASI phpMyAdmin
# =============================================================================
print_header "STEP 7/8 - Instalasi phpMyAdmin"

PMA_DIR="/usr/share/phpmyadmin"
PMA_VERSION="5.2.1"

# Setup user phpMyAdmin di MySQL
read -p "Masukkan username untuk login phpMyAdmin (contoh: dbadmin): " pma_user
read -sp "Masukkan password untuk user phpMyAdmin                   : " pma_pass
echo ""

print_info "Membuat user MySQL untuk phpMyAdmin..."
sudo mysql -e "CREATE USER IF NOT EXISTS '${pma_user}'@'localhost';"
sudo mysql -e "ALTER USER '${pma_user}'@'localhost' IDENTIFIED BY '${pma_pass}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO '${pma_user}'@'localhost' WITH GRANT OPTION;"
sudo mysql -e "FLUSH PRIVILEGES;"
print_step "User '${pma_user}' untuk phpMyAdmin berhasil disiapkan."

print_info "Instalasi / Re-instalasi phpMyAdmin v${PMA_VERSION}..."
sudo rm -rf "$PMA_DIR"
sudo mkdir -p "$PMA_DIR"
wget -qO /tmp/phpmyadmin.tar.gz "https://files.phpmyadmin.net/phpMyAdmin/${PMA_VERSION}/phpMyAdmin-${PMA_VERSION}-all-languages.tar.gz"
sudo tar -xzf /tmp/phpmyadmin.tar.gz -C /tmp/
sudo cp -r /tmp/phpMyAdmin-${PMA_VERSION}-all-languages/* "$PMA_DIR/"
sudo rm -rf /tmp/phpMyAdmin-${PMA_VERSION}-all-languages /tmp/phpmyadmin.tar.gz
print_step "phpMyAdmin terpasang."

# Konfigurasi phpMyAdmin (config.inc.php)
PMA_SECRET=$(openssl rand -base64 32 | tr -d '\n\r/=+' | cut -c 1-32)
sudo tee "$PMA_DIR/config.inc.php" > /dev/null <<EOF
<?php
\$cfg['blowfish_secret'] = '${PMA_SECRET}';
\$i = 0;
\$i++;
\$cfg['Servers'][\$i]['auth_type']       = 'cookie';
\$cfg['Servers'][\$i]['host']            = '127.0.0.1';
\$cfg['Servers'][\$i]['compress']        = false;
\$cfg['Servers'][\$i]['AllowNoPassword'] = false;
\$cfg['UploadDir'] = '';
\$cfg['SaveDir']   = '';
\$cfg['TempDir']   = '/tmp/phpmyadmin_tmp';
EOF

sudo mkdir -p /tmp/phpmyadmin_tmp
sudo chown -R $WEB_USER:$WEB_USER "$PMA_DIR" /tmp/phpmyadmin_tmp
sudo chmod -R 755 "$PMA_DIR" /tmp/phpmyadmin_tmp

print_step "Konfigurasi phpMyAdmin selesai."

# =============================================================================
# STEP 8: KONFIGURASI NGINX (Aplikasi + phpMyAdmin)
# =============================================================================
print_header "STEP 8/8 - Setup Nginx Virtual Host"

NGINX_CONF="server {
    listen 80;
    server_name ${app_domain};
    root ${APP_DIR}/public;

    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header X-XSS-Protection \"1; mode=block\";
    add_header X-Content-Type-Options \"nosniff\";

    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    # Aplikasi Utama
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # phpMyAdmin - akses via /phpmyadmin
    location /phpmyadmin {
        alias ${PMA_DIR}/;
        index index.php;
        location ~ ^/phpmyadmin/(.+\\.php)\$ {
            alias ${PMA_DIR}/\$1;
            fastcgi_pass unix:${PHP_SOCK};
            fastcgi_param SCRIPT_FILENAME ${PMA_DIR}/\$1;
            include fastcgi_params;
        }
        location ~* ^/phpmyadmin/(.+\\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))\$ {
            alias ${PMA_DIR}/\$1;
        }
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;

    location ~ \\.php\$ {
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}"

if [ -d "/etc/nginx/sites-available" ]; then
    echo "$NGINX_CONF" | sudo tee /etc/nginx/sites-available/steman-alumni > /dev/null
    sudo ln -sf /etc/nginx/sites-available/steman-alumni /etc/nginx/sites-enabled/steman-alumni
    sudo rm -f /etc/nginx/sites-enabled/default
    print_step "Nginx config: /etc/nginx/sites-available/steman-alumni"
else
    echo "$NGINX_CONF" | sudo tee /etc/nginx/conf.d/steman-alumni.conf > /dev/null
    print_step "Nginx config: /etc/nginx/conf.d/steman-alumni.conf"
fi

print_info "Validasi dan restart Nginx..."
sudo nginx -t && sudo systemctl restart nginx
print_step "Nginx berhasil direstart."

# =============================================================================
# SELESAI - RINGKASAN
# =============================================================================
print_header "✅  INSTALASI SELESAI!"

echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
echo -e "${BOLD}  INFORMASI AKSES — STEMAN Alumni v5${NC}"
echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
echo ""
echo -e "  🌐 ${BOLD}Aplikasi Web${NC}"
echo -e "     URL    : ${CYAN}${APP_URL}${NC}"
echo -e "     Login  : ${CYAN}${APP_URL}/login${NC}"
echo ""
echo -e "  🔐 ${BOLD}Akun Admin Aplikasi${NC}"
echo -e "     Email  : ${YELLOW}admin@steman.ac.id${NC}"
echo -e "     Pass   : ${YELLOW}Admin@1234${NC}"
echo ""
echo -e "  🗄️  ${BOLD}phpMyAdmin${NC}"
echo -e "     URL    : ${CYAN}${APP_URL}/phpmyadmin${NC}"
echo -e "     User   : ${YELLOW}${pma_user}${NC}"
echo -e "     Pass   : ${YELLOW}${pma_pass}${NC}"
echo ""
echo -e "  🗄️  ${BOLD}Database Aplikasi${NC}"
echo -e "     Nama DB: ${YELLOW}${db_name}${NC}"
echo -e "     User   : ${YELLOW}${db_user}${NC}"
echo -e "     Pass   : ${YELLOW}${db_pass}${NC}"
echo ""
echo -e "  🔑 ${BOLD}Social Login (v5 - Konfigurasi Manual)${NC}"
echo -e "     Edit .env dan isi:"
echo -e "     ${YELLOW}GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET${NC}"
echo -e "     ${YELLOW}GITHUB_CLIENT_ID, GITHUB_CLIENT_SECRET${NC}"
echo -e "     Kemudian: ${CYAN}php artisan config:cache${NC}"
echo ""
echo -e "${RED}${BOLD}  ⚠️  Segera ganti semua password default setelah login!${NC}"
echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
