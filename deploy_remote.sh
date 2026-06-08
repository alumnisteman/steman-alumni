#!/usr/bin/env bash
# ------------------------------------------------------------
# Deploy steman-alumni ke server 103.175.219.57 via SSH
# Jalankan: bash deploy_remote.sh
# ------------------------------------------------------------

set -e

REMOTE_HOST="103.175.219.57"
REMOTE_USER="root"
REMOTE_DIR="/var/www/steman-alumni"
REPO_URL="https://github.com/alumnisteman/steman-alumni.git"

echo "======================================================"
echo "  STEMAN ALUMNI — DEPLOY KE SERVER"
echo "  Host  : $REMOTE_USER@$REMOTE_HOST"
echo "  Dir   : $REMOTE_DIR"
echo "  Waktu : $(date)"
echo "======================================================"

# Fungsi helper: jalankan perintah di server via SSH
run_remote() {
    ssh -o StrictHostKeyChecking=no "${REMOTE_USER}@${REMOTE_HOST}" "$1"
}

# 1. Pastikan direktori ada
echo "[1/9] Memastikan direktori deploy ada..."
run_remote "mkdir -p ${REMOTE_DIR}"

# 2. Clone atau pull repository
echo "[2/9] Clone / pull repository terbaru..."
run_remote "
  cd ${REMOTE_DIR}
  if git rev-parse --is-inside-work-tree > /dev/null 2>&1; then
    git fetch origin
    git reset --hard origin/main 2>/dev/null || git reset --hard origin/master
  else
    git clone ${REPO_URL} .
  fi
"

# 3. Install Composer jika belum ada, lalu install dependencies
echo "[3/9] Install PHP dependencies..."
run_remote "
  if ! command -v composer > /dev/null 2>&1; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
  fi
  cd ${REMOTE_DIR}
  composer install --no-dev --optimize-autoloader --no-interaction
"

# 4. [FIX KRITIS] Buat symlink storage — ini penyebab foto tidak tampil
echo "[4/9] Membuat symlink storage (FIX foto tidak tampil)..."
run_remote "
  cd ${REMOTE_DIR}
  php artisan storage:link --force
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
"

# 5. Jalankan migrasi database
echo "[5/9] Menjalankan migrasi database..."
run_remote "cd ${REMOTE_DIR} && php artisan migrate --force"

# 6. Optimasi Laravel
echo "[6/9] Mengoptimasi Laravel (config, route, view cache)..."
run_remote "
  cd ${REMOTE_DIR}
  php artisan config:clear
  php artisan config:cache
  php artisan route:clear
  php artisan route:cache
  php artisan view:clear
  php artisan view:cache
  php artisan event:cache
"

# 7. Set admin role
echo "[7/9] Memastikan admin role sudah benar..."
run_remote "cd ${REMOTE_DIR} && php artisan tinker --execute=\"App\\\\Models\\\\User::where('email','valingir@gmail.com')->update(['role'=>'admin']);\""

# 8. Restart Docker containers
echo "[8/9] Restart Docker containers..."
run_remote "
  cd ${REMOTE_DIR}
  if [ -f docker-compose.prod.yml ]; then
    docker compose -f docker-compose.prod.yml up -d --build 2>/dev/null || true
    sleep 10
    docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link --force 2>/dev/null || true
    docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force 2>/dev/null || true
    docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache 2>/dev/null || true
    docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache 2>/dev/null || true
    docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache 2>/dev/null || true
  fi
"

# 9. Health check
echo "[9/9] Health check..."
sleep 5
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://alumni-steman.my.id/health" 2>/dev/null || echo "000")
echo "  HTTP Status: $HTTP_STATUS"
if [ "$HTTP_STATUS" = "200" ]; then
  echo "  Server merespons dengan baik!"
else
  echo "  Status $HTTP_STATUS (mungkin butuh waktu lebih lama atau gunakan Docker)"
fi

echo ""
echo "======================================================"
echo "  DEPLOY SELESAI! $(date)"
echo "  Museum: https://alumni-steman.my.id/museum"
echo "  Admin : https://admin.alumni-steman.my.id/museum"
echo "======================================================"
