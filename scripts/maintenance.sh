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
            -d text="📢 [$status] $task - STEMAN v5%0A%0A$message" > /dev/null
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
echo "[1/4] Mengoptimalkan Cache Laravel..."
# Use docker exec, but ensure container name matches
docker exec steman_app php artisan optimize:clear
docker exec steman_app php artisan optimize
docker exec steman_app php artisan route:cache
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan view:cache
echo "  -> Performa Cache diperbarui."

# --- 2. Log Management ---
echo "[2/4] Membersihkan Log Lama..."
# Kosongkan file log aplikasi
if [ -f "./storage/logs/laravel.log" ]; then
    echo "" > ./storage/logs/laravel.log
fi
# Clear docker container logs (optional, requires root)
# truncate -s 0 $(docker inspect --format='{{.LogPath}}' steman_app) 2>/dev/null
echo "  -> Log aplikasi (laravel.log) telah dikosongkan."

# --- 3. Docker Maintenance ---
echo "[3/4] Optimalisasi Container Docker..."
# Menghapus image yang tidak terpakai/dangled
docker image prune -f
echo "  -> Image tak terpakai telah dibersihkan."

# --- 4. Security & Version Check ---
echo "[4/4] Memeriksa Update Framework..."
VERSION=$(docker exec steman_app php artisan --version)
echo "  -> Pengecekan versi selesai: $VERSION"

notify "Pemeliharaan Sistem" "SUCCESS" "Optimalisasi cache, pembersihan log, dan pemangkasan Docker selesai dilakukan. Versi: $VERSION"

echo "====================================================="
echo " Pemeliharaan Selesai! (STEMAN Alumni Portal)"
echo "====================================================="
