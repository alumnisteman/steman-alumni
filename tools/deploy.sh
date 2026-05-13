#!/bin/bash
# ==============================================================
# STEMAN ALUMNI - UNIFIED DEPLOYMENT SCRIPT
# Version: 3.0 (Single Source of Truth)
# ==============================================================
set -e

APP_DIR="/var/www/steman-alumni"
LOG_FILE="$APP_DIR/deploy.log"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

echo "============================================"
echo " STEMAN ALUMNI DEPLOYMENT - $TIMESTAMP"
echo "============================================"
echo "[$TIMESTAMP] Deployment dimulai..." | tee -a "$LOG_FILE"

cd "$APP_DIR"

# --- 1. LINTING: Cek kesalahan sintaksis sebelum deploy ---
echo "[1/12] Menjalankan Linting Check (Syntax Check)..."
if ! docker run --rm --entrypoint sh -v "$(pwd):/app" steman-alumni-app:latest -c "find /app/app /app/routes /app/config /app/database -name '*.php' -exec php -l {} +"; then
    echo ""
    echo "  [ERROR] Kesalahan sintaksis ditemukan! Deployment dibatalkan."
    docker exec app php artisan steman:notify-maintenance error "Deployment dibatalkan karena kesalahan sintaksis PHP." || true
    echo "  Silakan perbaiki file PHP Anda dan coba lagi."
    echo "============================================"
    exit 1
fi
echo "  Sintaks OK."

# --- 2. LEGACY GUARD: Pastikan tidak ada kolom Bahasa Indonesia yang menyelinap kembali ---
echo "[2/12] Menjalankan Legacy Guard (Cek Kamus Larangan)..."
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

# --- 3. URL GUARD: Pastikan tidak ada URL hardcode yang berpotensi error ---
echo "[3/12] Menjalankan URL Guard (Cek URL Hardcode)..."
if grep -rE 'action="/(admin|alumni)/' resources/views/ | grep -v "URL Guard" > /dev/null; then
    echo ""
    echo "  [ERROR] URL Hardcode ditemukan di file Blade!"
    echo "  Detail temuan:"
    grep -rE 'action="/(admin|alumni)/' resources/views/ | head -n 5
    echo "  ..."
    echo "  Deployment dibatalkan. Gunakan fungsi route() untuk semua form action."
    echo "============================================"
    exit 1
fi
echo "  URL Guard OK."

# --- 4. Maintenance Mode ---
echo "[4/12] Mengaktifkan maintenance mode..."
docker exec app php artisan down --render="errors.503" --retry=60 2>/dev/null || echo "  (container belum up, skip)"

# --- 5. Build Image (UNIFIED — single Dockerfile) ---
echo "[5/12] Rebuild image Docker..."
docker build -t steman-alumni-app:latest -f Dockerfile . 2>&1 | tee -a "$LOG_FILE"

# --- 6. Restart Services (UNIFIED — single docker-compose.yml) ---
echo "[6/12] Restart containers..."
docker compose up -d --no-deps --force-recreate app
sleep 5
docker compose up -d --no-deps --force-recreate queue
docker compose up -d --no-deps --force-recreate reverb

# --- 7. NGINX VALIDATION (NEW: Prevent silent Nginx failures) ---
echo "[7/12] Validating Nginx configuration..."
# Force recreate webserver to pick up any config changes
docker compose up -d --no-deps --force-recreate webserver
sleep 3

# Wait for Nginx to stabilize (max 30 seconds)
NGINX_TRIES=0
NGINX_MAX=10
NGINX_OK=false
while [ $NGINX_TRIES -lt $NGINX_MAX ]; do
    NGINX_STATUS=$(docker inspect steman_nginx --format '{{.State.Status}}' 2>/dev/null || echo "missing")
    if [ "$NGINX_STATUS" = "running" ]; then
        # Additional check: make sure it's not crash-looping
        sleep 3
        NGINX_STATUS_2=$(docker inspect steman_nginx --format '{{.State.Status}}' 2>/dev/null || echo "missing")
        if [ "$NGINX_STATUS_2" = "running" ]; then
            NGINX_OK=true
            break
        fi
    fi
    echo "  Nginx status: $NGINX_STATUS (attempt $((NGINX_TRIES+1))/$NGINX_MAX)"
    sleep 3
    NGINX_TRIES=$((NGINX_TRIES+1))
done

if [ "$NGINX_OK" = false ]; then
    echo ""
    echo "  [CRITICAL] Nginx failed to stabilize!"
    echo "  Last 20 lines of Nginx log:"
    docker logs steman_nginx --tail 20 2>&1 || true
    docker exec app php artisan steman:notify-maintenance error "DEPLOYMENT GAGAL: Nginx tidak bisa start. Periksa konfigurasi segera!" || true
    echo "  Deployment GAGAL. Nginx tidak berjalan."
    exit 1
fi
echo "  Nginx: Running and stable ✓"

# Verify Nginx config syntax
if docker exec steman_nginx nginx -t 2>&1; then
    echo "  Nginx config syntax: OK ✓"
else
    echo "  [WARNING] Nginx config has warnings (but still running)"
fi

# --- 8. Migrations ---
echo "[8/12] Menjalankan migrations..."
sleep 5
docker exec app php artisan migrate --force

# --- 9. Optimization ---
echo "[9/12] Optimizing Laravel..."
docker exec app php artisan optimize:clear
docker exec app php artisan config:cache
docker exec app php artisan route:cache
docker exec app php artisan view:cache
docker exec app php artisan event:cache

# --- 10. POST-DEPLOYMENT VERIFICATION (Enhanced) ---
echo "[10/12] Menjalankan verifikasi sistem (Integrity & Tests)..."
if ! docker exec app php artisan steman:check-integrity; then
    echo "  [ERROR] Audit Integritas Gagal! Periksa output di atas."
    echo "  Aplikasi tetap dalam Maintenance Mode."
    docker exec app php artisan steman:notify-maintenance error "Deployment gagal: Integrity check tidak lolos." || true
    exit 1
fi

# Verify ALL containers are healthy (not just app)
echo "  Verifying container health..."
UNHEALTHY=$(docker ps --format '{{.Names}} {{.Status}}' | grep -E "Restarting|Exited|unhealthy" || true)
if [ -n "$UNHEALTHY" ]; then
    echo "  [WARNING] Unhealthy containers detected:"
    echo "  $UNHEALTHY"
fi

# Smoke Test (Optional if phpunit available)
if docker exec app [ -f vendor/bin/phpunit ]; then
    if ! docker exec app ./vendor/bin/phpunit --filter SystemIntegrityTest; then
        echo "  [ERROR] Smoke Test Gagal! Aplikasi tetap dalam Maintenance Mode."
        exit 1
    fi
    echo "  Smoke Test OK."
else
    echo "  [INFO] Smoke Test dilewati (PHPUnit tidak tersedia di environment ini)."
fi

# --- 11. Permissions ---
echo "[11/12] Applying permissions..."
docker exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# --- 12. Up ---
echo "[12/12] Menonaktifkan maintenance mode..."
docker exec app php artisan up

# --- FINAL: Notify & Log ---
echo "[FINAL] Mengirim laporan ke Telegram..."
docker exec app php artisan steman:notify-maintenance success "Deployment sukses. Semua sistem operational." || echo "  (Gagal kirim notifikasi)"

echo ""
echo "============================================"
echo " DEPLOYMENT SELESAI - $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deployment sukses." | tee -a "$LOG_FILE"
