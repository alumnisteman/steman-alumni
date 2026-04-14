#!/bin/bash
# ==============================================================
# STEMAN ALUMNI - ADVANCED DEPLOYMENT SCRIPT WITH LINTING
# Version: 2.3 (Robust Security Guards)
# ==============================================================
set -e

APP_DIR="/var/www/steman-alumni"
COMPOSE_FILE="$APP_DIR/docker-compose.prod.yml"
LOG_FILE="$APP_DIR/deploy.log"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

echo "============================================"
echo " STEMAN ALUMNI DEPLOYMENT - $TIMESTAMP"
echo "============================================"
echo "[$TIMESTAMP] Deployment dimulai..." | tee -a "$LOG_FILE"

cd "$APP_DIR"

# --- 1. LINTING: Cek kesalahan sintaksis sebelum deploy ---
echo "[1/8] Menjalankan Linting Check (Syntax Check)..."
# Menggunakan --entrypoint sh agar tidak memicu entrypoint asli yang menunggu DB
if ! docker run --rm --entrypoint sh -v "$(pwd):/app" steman-alumni-app:latest -c "find /app/app /app/routes /app/config /app/database -name '*.php' -exec php -l {} +"; then
    echo ""
    echo "  [ERROR] Kesalahan sintaksis ditemukan! Deployment dibatalkan."
    echo "  Silakan perbaiki file PHP Anda dan coba lagi."
    echo "============================================"
    exit 1
fi
echo "  Sintaks OK."

# --- 2. LEGACY GUARD: Pastikan tidak ada kolom Bahasa Indonesia yang menyelinap kembali ---
echo "[2/9] Menjalankan Legacy Guard (Cek Kamus Larangan)..."
LEGACY_WORDS="jurusan|tahun_lulus|pekerjaan_sekarang|perusahaan_universitas|nomor_telepon|foto_profil|pengirim_id|penerima_id|angkatan_tujuan"
if grep -rE "$LEGACY_WORDS" app/ routes/ config/ --exclude-dir=vendor | grep -v "Legacy Guard" > /dev/null; then
    echo ""
    echo "  [ERROR] Nama kolom legacy ditemukan di kode Anda!"
    echo "  Detail temuan:"
    grep -rE "$LEGACY_WORDS" app/ routes/ config/ --exclude-dir=vendor | head -n 5
    echo "  ..."
    echo "  Deployment dibatalkan. Gunakan nama kolom Standar (English)."
    echo "============================================"
    exit 1
fi
echo "  Standardisasi Kode OK."

# --- 2. Maintenance Mode ---
echo "[3/9] Mengaktifkan maintenance mode..."
docker exec steman-alumni-app-1 php artisan down --render="errors.503" --retry=60 2>/dev/null || echo "  (container belum up, skip)"

# --- 3. Build Image ---
echo "[4/9] Rebuild image Docker..."
docker build -t steman-alumni-app:latest -f Dockerfile.prod . 2>&1 | tee -a "$LOG_FILE"

# --- 4. Restart Services ---
echo "[5/9] Restart containers..."
docker compose -f "$COMPOSE_FILE" up -d --no-deps --force-recreate app
sleep 5
docker compose -f "$COMPOSE_FILE" up -d --no-deps --force-recreate queue
docker compose -f "$COMPOSE_FILE" up -d --no-deps --force-recreate reverb

# --- 5. Migrations ---
echo "[6/9] Menjalankan migrations..."
sleep 5
docker exec steman-alumni-app-1 php artisan migrate --force

# --- 6. Optimization ---
echo "[7/9] Optimizing Laravel..."
docker exec steman-alumni-app-1 php artisan optimize:clear
docker exec steman-alumni-app-1 php artisan config:cache
docker exec steman-alumni-app-1 php artisan route:cache
docker exec steman-alumni-app-1 php artisan view:cache
docker exec steman-alumni-app-1 php artisan event:cache

# --- 7. Permissions ---
echo "[8/9] Applying permissions..."
docker exec steman-alumni-app-1 chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# --- 8. Up ---
echo "[9/9] Menonaktifkan maintenance mode..."
docker exec steman-alumni-app-1 php artisan up

echo ""
echo "============================================"
echo " DEPLOYMENT SELESAI - $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deployment sukses." | tee -a "$LOG_FILE"
