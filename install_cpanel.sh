#!/bin/bash
# =============================================================================
#   STEMAN Alumni v5 - Script Instalasi cPanel/Shared Hosting
#   Dijalankan via SSH Terminal di server cPanel (atau cPanel Terminal)
#   Kompatibel dengan: cPanel, DirectAdmin, Plesk (mode terminal)
# =============================================================================
#
# CARA PENGGUNAAN:
#   1. Upload dan ekstrak file aplikasi ke public_html atau subfolder
#   2. Buka cPanel → Terminal (atau SSH ke server)
#   3. Masuk ke direktori aplikasi:  cd ~/public_html
#   4. Jalankan script:              bash install_cpanel.sh
#
# CATATAN: Script ini TIDAK memerlukan akses sudo/root.
#          Hanya menggunakan perintah yang tersedia di user-level cPanel.
# =============================================================================

set -e

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
print_warn()  { echo -e "${YELLOW}[!] $1${NC}"; }

# =============================================================================
print_header "STEMAN Alumni v5 - Installer cPanel/Shared Hosting"
# =============================================================================

# Cek file artisan (pastikan dijalankan dari folder yang benar)
if [ ! -f "artisan" ]; then
    print_error "Script harus dijalankan dari dalam direktori aplikasi STEMAN Alumni.
  Contoh: cd ~/public_html && bash install_cpanel.sh"
fi

APP_DIR=$(pwd)
HOME_DIR=$(echo ~)
print_info "Direktori Aplikasi : $APP_DIR"
print_info "Home Directory     : $HOME_DIR"

# =============================================================================
print_header "STEP 1/6 - Deteksi PHP dan Composer"
# =============================================================================

# Deteksi PHP yang tersedia di cPanel (PHP Selector)
PHP_BIN=""
for phpver in php8.2 php82 /usr/local/bin/php8.2 /opt/cpanel/ea-php82/root/usr/bin/php; do
    if command -v "$phpver" &>/dev/null; then
        PHP_BIN="$phpver"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    # Fallback ke PHP default
    PHP_BIN=$(command -v php 2>/dev/null || echo "")
fi

if [ -z "$PHP_BIN" ]; then
    print_error "PHP tidak ditemukan! Pastikan PHP 8.2 sudah diaktifkan di cPanel → Select PHP Version."
fi

PHP_VERSION=$($PHP_BIN -r "echo PHP_VERSION;")
print_step "PHP ditemukan: $PHP_BIN (v$PHP_VERSION)"

# Cek versi PHP minimal 8.2
if $PHP_BIN -r "exit(version_compare(PHP_VERSION, '8.2', '<') ? 1 : 0);"; then
    :
else
    print_error "PHP $PHP_VERSION terlalu lama! Butuh PHP 8.2+.
  Pergi ke cPanel → Select PHP Version dan pilih PHP 8.2."
fi

# Cek ekstensi penting
MISSING_EXT=""
for ext in pdo_mysql mbstring xml bcmath curl zip gd; do
    if ! $PHP_BIN -m | grep -q "^${ext}$" 2>/dev/null; then
        MISSING_EXT="$MISSING_EXT $ext"
    fi
done
if [ -n "$MISSING_EXT" ]; then
    print_warn "Ekstensi PHP berikut tidak aktif:$MISSING_EXT"
    print_warn "Aktifkan di cPanel → Select PHP Version → Extensions"
    read -p "Lanjutkan meskipun ada ekstensi tidak aktif? (y/n): " confirm_ext
    [ "$confirm_ext" != "y" ] && print_error "Instalasi dibatalkan."
fi

# Cek / Install Composer (tanpa sudo)
COMPOSER_BIN=""
if command -v composer &>/dev/null; then
    COMPOSER_BIN="composer"
elif [ -f "$HOME_DIR/composer.phar" ]; then
    COMPOSER_BIN="$PHP_BIN $HOME_DIR/composer.phar"
elif [ -f "$APP_DIR/composer.phar" ]; then
    COMPOSER_BIN="$PHP_BIN $APP_DIR/composer.phar"
else
    print_info "Mengunduh Composer ke home directory..."
    curl -sS https://getcomposer.org/installer | $PHP_BIN -- --install-dir="$HOME_DIR" --filename=composer.phar
    COMPOSER_BIN="$PHP_BIN $HOME_DIR/composer.phar"
fi
print_step "Composer: $COMPOSER_BIN"

# =============================================================================
print_header "STEP 2/6 - Konfigurasi Database"
# =============================================================================

echo -e "${YELLOW}Masukkan informasi database MySQL (buat dulu di cPanel → MySQL Databases):${NC}"
read -p "  Nama database   (contoh: cpanelusername_steman) : " db_name
read -p "  Username DB     (contoh: cpanelusername_user)   : " db_user
read -sp "  Password DB                                       : " db_pass
echo ""
read -p "  Host DB         (biasanya: localhost)             : " db_host
db_host="${db_host:-localhost}"

# Test koneksi database
print_info "Menguji koneksi database..."
if $PHP_BIN -r "
try {
    \$pdo = new PDO('mysql:host=${db_host};dbname=${db_name}', '${db_user}', '${db_pass}', [PDO::ATTR_TIMEOUT => 5]);
    echo 'OK';
} catch(Exception \$e) {
    echo 'FAIL:' . \$e->getMessage();
}
" | grep -q "^OK"; then
    print_step "Koneksi database berhasil."
else
    ERR=$($PHP_BIN -r "
try {
    \$pdo = new PDO('mysql:host=${db_host};dbname=${db_name}', '${db_user}', '${db_pass}', [PDO::ATTR_TIMEOUT => 5]);
} catch(Exception \$e) {
    echo \$e->getMessage();
}
")
    print_warn "Gagal terhubung ke database: $ERR"
    print_warn "Pastikan database dan user sudah dibuat di cPanel → MySQL Databases."
    read -p "Lanjutkan? (y/n): " confirm_db
    [ "$confirm_db" != "y" ] && print_error "Instalasi dibatalkan."
fi

# =============================================================================
print_header "STEP 3/6 - Konfigurasi Aplikasi Laravel (.env)"
# =============================================================================

read -p "Domain / URL aplikasi Anda (contoh: alumni.smkn2.sch.id): " app_domain
APP_URL="https://${app_domain}"

print_info "Membuat file .env..."
cp -f .env.example .env

sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|"         .env
sed -i "s|APP_ENV=.*|APP_ENV=production|"          .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|"           .env
sed -i "s|DB_HOST=.*|DB_HOST=${db_host}|"          .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${db_name}|"  .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${db_user}|"  .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${db_pass}|"  .env

# Untuk cPanel: driver queue & session harus database/sync (tidak ada Redis/Supervisor default)
sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|" .env
sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=database|"     .env
sed -i "s|CACHE_STORE=.*|CACHE_STORE=database|"           .env

print_step ".env berhasil dikonfigurasi."

# =============================================================================
print_header "STEP 4/6 - Instalasi Dependensi & Migrasi Database"
# =============================================================================

print_info "Install dependensi Composer..."
$COMPOSER_BIN install --optimize-autoloader --no-dev --no-interaction

print_info "Generate Application Key..."
$PHP_BIN artisan key:generate --force

print_info "Jalankan migrasi database..."
$PHP_BIN artisan migrate --force

print_info "Jalankan database seeder..."
$PHP_BIN artisan db:seed --force

print_info "Buat tabel untuk queue & session (wajib di cPanel)..."
$PHP_BIN artisan queue:table 2>/dev/null || true
$PHP_BIN artisan session:table 2>/dev/null || true
$PHP_BIN artisan cache:table 2>/dev/null || true
$PHP_BIN artisan migrate --force

print_info "Symlink storage..."
$PHP_BIN artisan storage:link || true

print_info "Optimasi cache..."
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

print_step "Laravel berhasil dikonfigurasi!"

# =============================================================================
print_header "STEP 5/6 - Mengatur Hak Akses Direktori"
# =============================================================================

find "$APP_DIR" -type f -exec chmod 644 {} \;
find "$APP_DIR" -type d -exec chmod 755 {} \;
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

print_step "Hak akses berhasil diatur."

# =============================================================================
print_header "STEP 6/6 - Konfigurasi Document Root"
# =============================================================================

# Cek apakah ada file .htaccess di root (untuk redirect ke /public)
if [ ! -f "$APP_DIR/.htaccess" ] || ! grep -q "RewriteEngine" "$APP_DIR/.htaccess" 2>/dev/null; then
    print_info "Membuat .htaccess di root untuk redirect ke public/..."
    cat > "$APP_DIR/.htaccess" <<'HTEOF'
# ─── SECURITY RULES (Blokir File Sensitif) ───
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

<FilesMatch "^(artisan|composer\.json|package\.json|\.env(.*)?)$">
    Require all denied
</FilesMatch>

# Blokir akses ke folder internal
RedirectMatch 404 ^/(storage/logs|vendor|node_modules|tests|app|bootstrap|config|database|resources|routes)(/|$)

# ─── ROUTING UTAMA KE LARAVEL PUBLIC ───
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTEOF
    print_step ".htaccess untuk redirect ke /public dibuat."
fi

# =============================================================================
print_header "✅  INSTALASI cPANEL SELESAI!"
# =============================================================================

echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
echo -e "${BOLD}  STEMAN Alumni v5 — Ringkasan Instalasi cPanel${NC}"
echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
echo ""
echo -e "  🌐 ${BOLD}Aplikasi Web${NC}"
echo -e "     URL   : ${CYAN}${APP_URL}${NC}"
echo -e "     Login : ${CYAN}${APP_URL}/login${NC}"
echo ""
echo -e "  🔐 ${BOLD}Akun Admin Default${NC}"
echo -e "     Email : ${YELLOW}admin@steman.ac.id${NC}"
echo -e "     Pass  : ${YELLOW}Admin@1234${NC}"
echo ""
echo -e "  📋 ${BOLD}LANGKAH LANJUTAN (Wajib)${NC}"
echo -e "     1. Di cPanel → Select PHP Version:"
echo -e "        Pastikan PHP 8.2 aktif + ekstensi: mbstring, xml, gd, zip, bcmath, curl, pdo_mysql"
echo -e "     2. Di cPanel → Cron Job → tambahkan queue worker:"
echo -e "        ${CYAN}* * * * *  ${PHP_BIN} ${APP_DIR}/artisan queue:work --max-time=55 --tries=3${NC}"
echo -e "     3. Isi kredensial Social Login di file .env:"
echo -e "        ${YELLOW}GOOGLE_CLIENT_ID=your-id${NC}"
echo -e "        ${YELLOW}GOOGLE_CLIENT_SECRET=your-secret${NC}"
echo -e "     4. Jika memakai subdomain, arahkan document root cPanel ke:"
echo -e "        ${CYAN}${APP_DIR}/public${NC}"
echo -e "        (Menu: cPanel → Addon Domains / Subdomains → Document Root)"
echo ""
echo -e "${RED}${BOLD}  ⚠️  Segera ganti password admin setelah login!${NC}"
echo -e "${BOLD}═══════════════════════════════════════════════${NC}"
