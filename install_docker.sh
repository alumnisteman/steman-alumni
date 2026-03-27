#!/bin/bash
# Script Instalasi Docker STEMAN Alumni v5
# Mendukung: Social Login, Reverb (WebSocket), PWA, Analytics

echo "====================================================="
echo "   STEMAN Alumni v5 - Docker Auto Installer"
echo "====================================================="

# Cek ketersediaan Docker
if ! command -v docker &> /dev/null; then
    echo "Error: Docker belum terinstal!"
    echo "Instal Docker from: https://docs.docker.com/get-docker/"
    exit 1
fi

# Detect docker compose command
if docker compose version &> /dev/null 2>&1; then
    DOCKER_CMD="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_CMD="docker-compose"
else
    echo "Error: Docker Compose belum terinstal!"
    exit 1
fi
echo "Menggunakan: $DOCKER_CMD"

echo "[1/4] Menyiapkan file konfigurasi dan mencari port kosong..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "  -> File .env berhasil dibuat dari .env.example."
fi

# Fungsi cari port kosong
get_free_port() {
    local port=$1
    while netstat -an 2>/dev/null | grep -qE "[\.:]$port\b.*LISTEN" || ss -ltn 2>/dev/null | grep -qE "[\.:]$port\b" || lsof -i :$port -sTCP:LISTEN 2>/dev/null | grep -q LISTEN; do
        port=$((port+1))
    done
    echo $port
}

# Cek ketersediaan command pencari port
if ! command -v netstat &>/dev/null && ! command -v ss &>/dev/null && ! command -v lsof &>/dev/null; then
    echo "Peringatan: netstat, ss, atau lsof tidak ditemukan. Menggunakan port default."
    APP_PORT=8000
    SSL_PORT=8443
else
    APP_PORT=$(get_free_port 8000)
    SSL_PORT=$(get_free_port 8443)
fi

echo "  -> Port HTTP Dialokasikan : $APP_PORT"
echo "  -> Port HTTPS Dialokasikan: $SSL_PORT"

# Konfigurasi Dasar Docker
sed -i "s|^DB_HOST=.*|DB_HOST=db|" .env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=steman_alumni|" .env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=steman_user|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=secret123|" .env
sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|^APP_URL=.*|APP_URL=http://localhost:$APP_PORT|" .env

# Hapus dan tambahkan variable port docker
sed -i "/^APP_PORT/d" .env || true
sed -i "/^SSL_PORT/d" .env || true
echo "APP_PORT=$APP_PORT" >> .env
echo "SSL_PORT=$SSL_PORT" >> .env

echo "[2/4] Membangun image dan menjalankan container..."
echo "      (Proses ini mungkin memakan waktu beberapa menit untuk build pertama)"
$DOCKER_CMD up -d --build

echo "[3/4] Menunggu sistem siap dan sehat (Healthcheck)..."
# Loop menunggu status healthy dari container app
MAX_RETRIES=60
RETRY=0
while [ $RETRY -lt $MAX_RETRIES ]; do
    STATUS=$(docker inspect --format='{{json .State.Health.Status}}' steman_app 2>/dev/null | tr -d '"')
    if [ "$STATUS" = "healthy" ]; then
        echo "  -> Sistem terdeteksi SEHAT (Healthy)!"
        break
    fi
    echo "  -> Menunggu inisialisasi sistem... ($RETRY/$MAX_RETRIES)"
    sleep 5
    RETRY=$((RETRY+1))
done

if [ $RETRY -eq $MAX_RETRIES ]; then
    echo "Peringatan: Sistem memakan waktu lebih lama untuk inisialisasi."
    echo "Cek log dengan: $DOCKER_CMD logs app"
fi

echo "[4/4] Finalisasi dan Pembersihan..."
$DOCKER_CMD exec -T app php artisan optimize:clear > /dev/null

echo "====================================================="
echo " Instalasi Docker Selesai! (STEMAN Alumni v5)"
echo " Status: PRODUCTION READY"
echo "====================================================="
echo ""
echo " Akses Aplikasi:"
echo "   URL Utama : http://localhost:$APP_PORT"
echo "   Login Admin: http://localhost:$APP_PORT/login"
echo ""
echo " Kredensial Admin Default:"
echo "   Email  : admin@steman.ac.id"
echo "   Pass   : Admin@1234"
echo ""
echo " INSTRUKSI PENTING:"
echo "   1. Segera ganti Password Admin setelah login pertama."
echo "   2. Untuk backup data, jalankan: bash scripts/backup.sh"
echo "   3. Untuk panduan maintenance: lihat TUTORIAL_MAINTENANCE.md"
echo "====================================================="
