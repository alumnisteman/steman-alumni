#!/bin/bash
# =============================================================================
#   STEMAN Alumni v5 - Host Nginx Reverse Proxy Setup
#   Gunakan ini jika server VPS Anda sudah memiliki Nginx/Apache di port 80/443
#   dan aplikasi Docker terpaksa berjalan di port lain (misal: 8000).
# =============================================================================

echo "====================================================="
echo "   Nginx Reverse Proxy Auto-Config - STEMAN Alumni"
echo "====================================================="

if [ "$EUID" -ne 0 ]; then
  echo "Harap jalankan script ini dengan sudo (sudo bash setup_reverse_proxy.sh)"
  exit 1
fi

if ! command -v nginx &> /dev/null; then
  echo "❌ Nginx belum terinstal di server host ini."
  echo "   Install Nginx terlebih dahulu (apt install nginx / dnf install nginx)"
  exit 1
fi

# Membaca APP_PORT dari .env Docker
if [ -f ".env" ]; then
    APP_PORT=$(grep "^APP_PORT=" .env | cut -d '=' -f2)
fi
APP_PORT=${APP_PORT:-8000}

read -p "Masukkan Domain/Subdomain (contoh: alumni.smkn2ternate.sch.id): " domain_name
read -p "Masukkan Port Docker Aplikasi (Default $APP_PORT): " input_port
APP_PORT=${input_port:-$APP_PORT}

echo ""
echo "Opsi HTTPS/SSL pada Reverse Proxy Host:"
echo "  1) HTTP Saja (Port 80 ke Docker $APP_PORT)"
echo "  2) HTTPS dengan Let's Encrypt (Certbot Host)"
read -p "Pilihan (1/2): " ssl_choice

# Detect OS
if [ -d "/etc/nginx/sites-available" ]; then
    NGINX_CONF_PATH="/etc/nginx/sites-available/proxy-$domain_name"
    NGINX_ENABLED_PATH="/etc/nginx/sites-enabled/proxy-$domain_name"
    IS_UBUNTU=true
else
    NGINX_CONF_PATH="/etc/nginx/conf.d/proxy-$domain_name.conf"
    IS_UBUNTU=false
fi

# =============================================================================
# Membuat Blok Konfigurasi Host Nginx
# =============================================================================
build_proxy_block() {
    local listen_directive=$1

    cat <<EOF
server {
    ${listen_directive}
    server_name ${domain_name};

    # ─── Meneruskan Traffic ke Docker Nginx ───────────────
    location / {
        proxy_pass http://127.0.0.1:${APP_PORT};
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        
        proxy_read_timeout 60s;
        proxy_connect_timeout 60s;
    }

    # ─── Meneruskan WebSocket (Laravel Reverb) ────────────
    location /app {
        proxy_pass http://127.0.0.1:${APP_PORT};
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_read_timeout 60s;
        proxy_cache_bypass \$http_upgrade;
    }

    # Bypass upload error (maks ukuran file)
    client_max_body_size 20M;
}
EOF
}

# ─── MODE HTTP SAJA ──────────────────────────────────────────────
if [ "$ssl_choice" != "2" ]; then
    echo "[→] Menulis konfigurasi Reverse Proxy HTTP..."
    build_proxy_block "listen 80;" > "$NGINX_CONF_PATH"

    if $IS_UBUNTU; then
        ln -sf "$NGINX_CONF_PATH" "$NGINX_ENABLED_PATH"
    fi

    nginx -t && systemctl restart nginx
    echo "====================================================="
    echo " ✅ BERHASIL! Reverse Proxy HTTP Aktif."
    echo "    Traffic ke $domain_name diarahkan ke localhost:$APP_PORT"
    echo "====================================================="
    exit 0
fi

# ─── MODE HTTPS (CERTBOT HOST) ───────────────────────────────────
echo "[→] Mode HTTPS Reverse Proxy dipilih..."

if ! command -v certbot &>/dev/null; then
    echo "[→] Menginstal Certbot di Host..."
    if $IS_UBUNTU; then
        apt-get install -y certbot python3-certbot-nginx -q
    else
        dnf install -y certbot python3-certbot-nginx
    fi
fi

# Tulis config HTTP dulu agar Certbot bisa verifikasi Let's Encrypt
build_proxy_block "listen 80;" > "$NGINX_CONF_PATH"
if $IS_UBUNTU; then
    ln -sf "$NGINX_CONF_PATH" "$NGINX_ENABLED_PATH"
fi
nginx -t && systemctl reload nginx

read -p "Email untuk notifikasi SSL Let's Encrypt: " ssl_email

echo "[→] Mendapatkan sertifikat SSL Host..."
certbot certonly --nginx \
    -d "$domain_name" \
    --email "$ssl_email" \
    --agree-tos \
    --non-interactive \
    --redirect

CERT_PATH="/etc/letsencrypt/live/${domain_name}"

if [ ! -f "${CERT_PATH}/fullchain.pem" ]; then
    echo "❌ Gagal mendapatkan sertifikat SSL dari Let's Encrypt!"
    echo "   Pastikan domain sudah diarahkan (A/AAAA record) ke IP Server ini."
    exit 1
fi

echo "[→] Sertifikat SSL Berhasil! Menyesuaikan Proxy ke HTTPS..."

# Tulis ulang konfigurasi dengan SSL
cat > "$NGINX_CONF_PATH" <<CERTEOF
# Redirect HTTP -> HTTPS
server {
    listen 80;
    server_name ${domain_name};
    return 301 https://\$host\$request_uri;
}

# Blok Utama HTTPS Reverse Proxy
server {
    listen 443 ssl http2;
    server_name ${domain_name};

    ssl_certificate     ${CERT_PATH}/fullchain.pem;
    ssl_certificate_key ${CERT_PATH}/privkey.pem;
    ssl_trusted_certificate ${CERT_PATH}/chain.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers off;
    
    # ─── Meneruskan Traffic ke Docker Nginx ───────────────
    location / {
        proxy_pass http://127.0.0.1:${APP_PORT};
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
    }

    # ─── Meneruskan WebSocket (Laravel Reverb) ────────────
    location /app {
        proxy_pass http://127.0.0.1:${APP_PORT};
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

    client_max_body_size 20M;
}
CERTEOF

certbot renew --dry-run &>/dev/null
nginx -t && systemctl restart nginx

echo "====================================================="
echo " ✅ BERHASIL! Reverse Proxy HTTPS Aktif."
echo ""
echo " 🌐 https://${domain_name} -> Docker localhost:${APP_PORT}"
echo ""
echo " PENTING UNTUK DOCKER:"
echo "   Pastikan di file .env Docker Anda ter-set:"
echo "     APP_URL=https://${domain_name}"
echo "     REVERB_SCHEME=https"
echo "     REVERB_HOST=${domain_name}"
echo "   Lalu jalankan di docker: docker compose exec app php artisan config:cache"
echo "====================================================="
