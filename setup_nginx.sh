#!/bin/bash
# Script Konfigurasi Otomatis Nginx Virtual Host - STEMAN Alumni v5
# Mendukung: HTTP, HTTPS (Let's Encrypt/SSL), Reverb (WebSocket), PWA

echo "====================================================="
echo "   Nginx Auto-Configurator - STEMAN Alumni v5"
echo "====================================================="

# 1. Pastikan dijalankan sebagai root/sudo
if [ "$EUID" -ne 0 ]; then
  echo "Harap jalankan script ini dengan sudo (sudo bash setup_nginx.sh)"
  exit 1
fi

# 2. Ambil informasi dari User
read -p "Masukkan Domain Server (contoh: alumni.smkn2ternate.sch.id): " domain_name

current_dir=$(pwd)
if [ "$current_dir" == "/var/www" ]; then
    default_root="/var/www/public"
else
    default_root="$current_dir/public"
fi

read -p "Masukkan Path ROOT Web (Default: $default_root): " public_path
public_path=${public_path:-$default_root}

# 3. Tanya port Laravel Reverb (WebSocket)
read -p "Port Laravel Reverb WebSocket (Default: 8080): " reverb_port
reverb_port=${reverb_port:-8080}

# 4. Tanya mode SSL
echo ""
echo "Pilih mode instalasi:"
echo "  1) HTTP saja (port 80)"
echo "  2) HTTPS dengan Let's Encrypt / Certbot (port 443)"
read -p "Pilihan (1/2): " ssl_choice

# 5. Deteksi Versi PHP-FPM socket
php_socket=$(find /var/run/php/ -name "php*-fpm.sock" 2>/dev/null | head -n 1)
if [ -z "$php_socket" ]; then
    if [ -f /var/run/php-fpm/www.sock ]; then
        php_socket="/var/run/php-fpm/www.sock"
    else
        php_socket="/var/run/php/php8.2-fpm.sock"
    fi
fi

echo ""
echo "Konfigurasi:"
echo " - Domain     : $domain_name"
echo " - Path       : $public_path"
echo " - PHP Socket : $php_socket"
echo " - Reverb     : :$reverb_port"
echo " - SSL        : $([ "$ssl_choice" == "2" ] && echo 'YA (HTTPS)' || echo 'TIDAK (HTTP saja)')"
echo ""

# =============================================================================
# FUNGSI: buat blok konfigurasi server Nginx
# =============================================================================
build_server_block() {
    local listen_directive=$1  # "listen 80;" atau "listen 443 ssl;"

    cat <<EOF
server {
    ${listen_directive}
    server_name ${domain_name};
    root ${public_path};

    # ─── Security Headers ─────────────────────────────────
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()";

    # ─── Performa ─────────────────────────────────────────
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    # Gzip Compression (untuk PWA & assets)
    gzip on;
    gzip_types text/plain text/css application/javascript application/json image/svg+xml;
    gzip_min_length 1024;

    # ─── Routing Laravel ──────────────────────────────────
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # ─── WebSocket - Laravel Reverb ───────────────────────
    location /app {
        proxy_pass http://127.0.0.1:${reverb_port};
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
        proxy_read_timeout 60s;
    }

    # ─── Aset Statis (Cache panjang untuk PWA) ────────────
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|webp|webmanifest)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;

    # ─── PHP Processing ───────────────────────────────────
    location ~ \.php\$ {
        fastcgi_pass unix:${php_socket};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
}

# =============================================================================
# PASANG KONFIGURASI
# =============================================================================

# Tentukan path konfigurasi Nginx (Ubuntu vs CentOS)
if [ -d "/etc/nginx/sites-available" ]; then
    NGINX_CONF_PATH="/etc/nginx/sites-available/steman-alumni"
    NGINX_ENABLED_PATH="/etc/nginx/sites-enabled/steman-alumni"
    IS_UBUNTU=true
else
    NGINX_CONF_PATH="/etc/nginx/conf.d/steman-alumni.conf"
    IS_UBUNTU=false
fi

# ─── MODE HTTP saja ──────────────────────────────────────────────────────────
if [ "$ssl_choice" != "2" ]; then
    echo "[→] Menulis konfigurasi HTTP..."
    build_server_block "listen 80;" > "$NGINX_CONF_PATH"

    if $IS_UBUNTU; then
        ln -sf "$NGINX_CONF_PATH" "$NGINX_ENABLED_PATH"
        rm -f /etc/nginx/sites-enabled/default
    fi

    nginx -t && systemctl restart nginx

    echo "====================================================="
    echo " BERHASIL! Website aktif di: http://${domain_name}"
    echo ""
    echo " TIP: Untuk upgrade ke HTTPS, jalankan:"
    echo "   sudo certbot --nginx -d ${domain_name}"
    echo "====================================================="
    exit 0
fi

# ─── MODE HTTPS (Let's Encrypt) ──────────────────────────────────────────────
echo "[→] Mode HTTPS dipilih."
echo ""

# Cek Certbot
if ! command -v certbot &>/dev/null; then
    echo "[→] Certbot tidak ditemukan. Menginstal..."
    if $IS_UBUNTU; then
        apt-get install -y certbot python3-certbot-nginx -q
    else
        dnf install -y certbot python3-certbot-nginx
    fi
fi

# Buat dulu config HTTP agar certbot bisa verifikasi domain
echo "[→] Membuat konfigurasi HTTP sementara untuk verifikasi domain..."
build_server_block "listen 80;" > "$NGINX_CONF_PATH"

if $IS_UBUNTU; then
    ln -sf "$NGINX_CONF_PATH" "$NGINX_ENABLED_PATH"
    rm -f /etc/nginx/sites-enabled/default
fi

nginx -t && systemctl reload nginx

read -p "Email untuk notifikasi SSL Let's Encrypt: " ssl_email

echo "[→] Mendapatkan sertifikat SSL dari Let's Encrypt..."
certbot certonly --nginx \
    -d "$domain_name" \
    --email "$ssl_email" \
    --agree-tos \
    --non-interactive \
    --redirect

CERT_PATH="/etc/letsencrypt/live/${domain_name}"

if [ ! -f "${CERT_PATH}/fullchain.pem" ]; then
    echo "[✘] Gagal mendapatkan sertifikat SSL!"
    echo "    Pastikan domain ${domain_name} sudah diarahkan ke IP server ini."
    exit 1
fi

echo "[→] Sertifikat SSL berhasil! Menulis konfigurasi HTTPS..."

# Tulis konfigurasi final: redirect HTTP → HTTPS + server HTTPS
cat > "$NGINX_CONF_PATH" <<CERTEOF
# ─── Redirect HTTP → HTTPS ────────────────────────────────────────────────
server {
    listen 80;
    server_name ${domain_name};
    return 301 https://\$host\$request_uri;
}

# ─── HTTPS Server ─────────────────────────────────────────────────────────
server {
    listen 443 ssl http2;
    server_name ${domain_name};
    root ${public_path};

    # ─── SSL Sertifikat ───────────────────────────────────
    ssl_certificate     ${CERT_PATH}/fullchain.pem;
    ssl_certificate_key ${CERT_PATH}/privkey.pem;
    ssl_trusted_certificate ${CERT_PATH}/chain.pem;

    # ─── SSL Modern Config ────────────────────────────────
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers off;
    ssl_session_timeout 1d;
    ssl_session_cache shared:MozSSL:10m;
    ssl_stapling on;
    ssl_stapling_verify on;

    # ─── Security Headers ─────────────────────────────────
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;

    # ─── Performa ─────────────────────────────────────────
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    gzip on;
    gzip_types text/plain text/css application/javascript application/json image/svg+xml;
    gzip_min_length 1024;

    # ─── Routing Laravel ──────────────────────────────────
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # ─── WebSocket - Laravel Reverb (wss://) ──────────────
    location /app {
        proxy_pass http://127.0.0.1:${reverb_port};
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_cache_bypass \$http_upgrade;
        proxy_read_timeout 60s;
    }

    # ─── Aset Statis (Cache panjang untuk PWA) ────────────
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|webp|webmanifest)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;

    # ─── PHP Processing ───────────────────────────────────
    location ~ \.php\$ {
        fastcgi_pass unix:${php_socket};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
CERTEOF

# Aktifkan auto-renewal
echo "[→] Mengaktifkan auto-renewal SSL..."
certbot renew --dry-run &>/dev/null && echo "[✔] Auto-renewal siap (cron Certbot aktif)."

nginx -t && systemctl restart nginx

echo "====================================================="
echo " BERHASIL! Website aktif di: https://${domain_name}"
echo ""
echo " SSL Info:"
echo "   Sertifikat : ${CERT_PATH}/fullchain.pem"
echo "   Berlaku    : 90 hari (auto-renew aktif)"
echo ""
echo " CATATAN v5 HTTPS:"
echo "   • WebSocket Reverb berjalan via wss:// (port 443)"
echo "   • Update .env: APP_URL=https://${domain_name}"
echo "   • Update .env: REVERB_SCHEME=https"
echo "   • Jalankan: php artisan config:cache"
echo "   • HSTS aktif (browser paksa HTTPS selama 2 tahun)"
echo "====================================================="
