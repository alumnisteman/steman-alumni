#!/bin/bash
# Script Pemeliharaan STEMAN Alumni v5 (Docker)
# Lokasi: /var/www/steman-alumni/scripts/maintenance.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# --- Load Environment Variables ---
if [ -f .env ]; then
    # Load and clean variables
    export $(grep -v '^#' .env | xargs)
fi

# --- Notification Function ---
notify() {
    local task=$1
    local status=$2
    local message=$3
    
    echo "  -> Mengirim notifikasi ($status)..."
    
    # 1. Telegram Notification (via curl)
    if [ ! -z "$TELEGRAM_BOT_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
        curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
            -d chat_id="$TELEGRAM_CHAT_ID" \
            -d text="???? [$status] $task - STEMAN v5%0A%0A$message" > /dev/null
    fi
    
    # 2. Email Notification (via Laravel Artisan)
    if docker exec steman_app php artisan list | grep -q "steman:notify-status"; then
        docker exec steman_app php artisan steman:notify-status "$task" "$status" "$message" > /dev/null
    fi
}

echo "====================================================="
echo "   STEMAN Alumni v5 - Maintenance System"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- 1. Laravel Performance Optimization ---
echo "[1/5] Mengoptimalkan Cache Laravel..."
# Use docker exec, but ensure container name matches
docker exec steman-alumni-app-1 php artisan optimize:clear
docker exec steman-alumni-app-1 php artisan optimize
docker exec steman-alumni-app-1 php artisan route:cache
docker exec steman-alumni-app-1 php artisan config:cache
docker exec steman-alumni-app-1 php artisan view:cache
echo "  -> Performa Cache diperbarui."

# --- 2. Log Management ---
echo "[2/5] Membersihkan Log Lama..."
# Kosongkan file log aplikasi
if [ -f "./storage/logs/laravel.log" ]; then
    echo "" > ./storage/logs/laravel.log
fi
if [ -f "./storage/logs/emergency_fatal.log" ]; then
    echo "" > ./storage/logs/emergency_fatal.log
fi
# Clear docker container logs (optional, requires root)
# truncate -s 0 $(docker inspect --format='{{.LogPath}}' steman-alumni-app-1) 2>/dev/null
echo "  -> Log aplikasi telah dikosongkan."

# --- 3. Docker Maintenance ---
echo "[3/5] Optimalisasi Container Docker..."
# Menghapus image yang tidak terpakai/dangled
docker image prune -f
echo "  -> Image tak terpakai telah dibersihkan."

# --- 4. Database Maintenance ---
echo "[4/5] Optimalisasi Database..."
# Analyze tables for optimization
docker exec steman_db mysql -uroot -pCh4v4run3@ steman_alumni -e "ANALYZE TABLE users, posts, activity_logs, likes, comments;" 2>/dev/null
echo "  -> Database tables analyzed."

# --- 5. Security & Version Check ---
echo "[5/5] Memeriksa Update Framework..."
VERSION=$(docker exec steman-alumni-app-1 php artisan --version)
echo "  -> Pengecekan versi selesai: $VERSION"

notify "Pemeliharaan Sistem" "SUCCESS" "Optimalisasi cache, pembersihan log, pemangkasan Docker, dan optimasi database selesai dilakukan. Versi: $VERSION"

echo "====================================================="
echo " Pemeliharaan Selesai! (STEMAN Alumni Portal)"
echo "====================================================="
