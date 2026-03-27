#!/bin/bash
# =============================================================================
#   STEMAN Alumni v5 - Script SSL Docker (Let's Encrypt + Nginx)
#   Jalankan SETELAH install_docker.sh berhasil dan container berjalan
#   Prasyarat: Domain sudah diarahkan ke IP server ini
# =============================================================================
# CARA PENGGUNAAN:
#   bash install_docker_ssl.sh
# =============================================================================

set -e

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

print_step()  { echo -e "${GREEN}[✔] $1${NC}"; }
print_info()  { echo -e "${YELLOW}[→] $1${NC}"; }
print_error() { echo -e "${RED}[✘] ERROR: $1${NC}"; exit 1; }

echo ""
echo -e "${CYAN}${BOLD}=============================================${NC}"
echo -e "${CYAN}${BOLD}  STEMAN Alumni v5 - SSL Docker Installer${NC}"
echo -e "${CYAN}${BOLD}=============================================${NC}"
echo ""

# Cek artisan (harus dari dalam direktori aplikasi)
if [ ! -f "artisan" ]; then
    print_error "Jalankan dari dalam direktori aplikasi STEMAN Alumni."
fi

# Deteksi docker compose command
if docker compose version &>/dev/null 2>&1; then
    DOCKER_CMD="docker compose"
elif command -v docker-compose &>/dev/null; then
    DOCKER_CMD="docker-compose"
else
    print_error "Docker Compose belum terinstal!"
fi

# Cek container webserver berjalan
if ! $DOCKER_CMD ps | grep -q "steman_nginx"; then
    print_error "Container steman_nginx tidak berjalan!
  Jalankan terlebih dahulu: bash install_docker.sh"
fi

# Peringatan Let's Encrypt Port 80 & 443
if [ -f ".env" ]; then
    APP_PORT=$(grep "^APP_PORT=" .env | cut -d '=' -f2)
    SSL_PORT=$(grep "^SSL_PORT=" .env | cut -d '=' -f2)
    if [ "$APP_PORT" != "80" ]; then
        echo -e "${YELLOW}⚠️ PERINGATAN: APP_PORT Docker Anda adalah $APP_PORT (bukan 80).${NC}"
        echo -e "${YELLOW}   Let's Encrypt (Certbot) di dalam Docker butuh akses langsung ke port 80 pada domain.${NC}"
        echo -e "${YELLOW}   ${BOLD}PENTING:${NC}${YELLOW} Jika Anda sudah menggunakan naskah 'setup_reverse_proxy.sh' untuk server Host,${NC}"
        echo -e "${YELLOW}   Anda TIDAK PERLU dan TIDAK BOLEH menjalankan script ini! SSL sudah ditangani oleh Host Nginx.${NC}"
        echo ""
        read -p "Apakah Anda YAKIN ingin melanjutkan instalasi SSL di dalam Docker? (y/N) " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${CYAN}Dibatalkan. Jika pakai Reverse Proxy Host, biarkan Docker tetap berjalan di port $APP_PORT (HTTP).${NC}"
            exit 1
        fi
    fi
fi

# =============================================================================
print_info "Input informasi SSL..."
# =============================================================================
read -p "Masukkan domain (contoh: alumni.smkn2ternate.sch.id): " DOMAIN
read -p "Masukkan email untuk notifikasi SSL Let's Encrypt  : " EMAIL

echo ""
print_info "Konfigurasi:"
echo "  Domain : $DOMAIN"
echo "  Email  : $EMAIL"
echo ""

# =============================================================================
print_info "[1/4] Mendapatkan sertifikat SSL dari Let's Encrypt..."
# =============================================================================

# Pastikan certbot_www volume ada untuk ACME challenge
$DOCKER_CMD run --rm \
    -v certbot_certs:/etc/letsencrypt \
    -v certbot_www:/var/www/certbot \
    certbot/certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email "$EMAIL" \
    --agree-tos \
    --no-eff-email \
    -d "$DOMAIN"

# Verifikasi cert berhasil
if ! $DOCKER_CMD run --rm \
    -v certbot_certs:/etc/letsencrypt \
    certbot/certbot certificates 2>&1 | grep -q "$DOMAIN"; then
    print_error "Sertifikat untuk $DOMAIN tidak ditemukan!
  Pastikan domain $DOMAIN sudah diarahkan ke IP server ini."
fi

print_step "Sertifikat SSL berhasil didapatkan."

# =============================================================================
print_info "[2/4] Mengaktifkan konfigurasi Nginx HTTPS..."
# =============================================================================

# Salin template app-ssl.conf dan ganti placeholder domain
cp docker/nginx/conf.d/app-ssl.conf docker/nginx/conf.d/app-ssl.conf.bak 2>/dev/null || true
sed "s/YOUR_DOMAIN_HERE/$DOMAIN/g" docker/nginx/conf.d/app-ssl.conf > /tmp/steman-ssl-nginx.conf
cp /tmp/steman-ssl-nginx.conf docker/nginx/conf.d/app-ssl.conf

# Hapus konfigurasi HTTP-only (digantikan oleh app-ssl.conf yang sudah ada redirect)
# Konfigurasi app.conf tetap ada untuk fallback/certbot, tapi matikan server block-nya
print_step "Konfigurasi Nginx HTTPS aktif."

# =============================================================================
print_info "[3/4] Update APP_URL dan REVERB_SCHEME di .env..."
# =============================================================================
if [ -f ".env" ]; then
    sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
    sed -i "s|REVERB_SCHEME=.*|REVERB_SCHEME=https|" .env
    sed -i "s|REVERB_HOST=.*|REVERB_HOST=${DOMAIN}|" .env
    print_step ".env diperbarui (APP_URL, REVERB_SCHEME, REVERB_HOST)."
fi

# =============================================================================
print_info "[4/4] Restart Nginx dan clear Laravel cache..."
# =============================================================================
$DOCKER_CMD restart webserver
sleep 3

$DOCKER_CMD exec -T app php artisan config:cache
$DOCKER_CMD exec -T app php artisan route:cache

print_step "Nginx direstart, cache Laravel diperbarui."

# =============================================================================
echo ""
echo -e "${GREEN}${BOLD}=============================================${NC}"
echo -e "${GREEN}${BOLD}  ✅  SSL DOCKER BERHASIL DIAKTIFKAN!${NC}"
echo -e "${GREEN}${BOLD}=============================================${NC}"
echo ""
echo -e "  🌐 URL Aplikasi : ${CYAN}https://${DOMAIN}${NC}"
echo ""
echo -e "  📋 Info SSL:"
echo -e "     • Sertifikat Let's Encrypt (90 hari)"
echo -e "     • Auto-renew: ${CYAN}$DOCKER_CMD up -d certbot${NC}"
echo -e "     • Manual renew: ${CYAN}$DOCKER_CMD run --rm certbot/certbot renew${NC}"
echo ""
echo -e "  📋 Langkah selanjutnya:"
echo -e "     1. Update Authorized redirect URI di Google Console:"
echo -e "        ${YELLOW}https://${DOMAIN}/auth/google/callback${NC}"
echo -e "     2. Update .env GOOGLE_REDIRECT_URI:"
echo -e "        ${YELLOW}GOOGLE_REDIRECT_URI=https://${DOMAIN}/auth/google/callback${NC}"
echo -e "     3. Jalankan: ${CYAN}$DOCKER_CMD exec app php artisan config:cache${NC}"
echo ""
