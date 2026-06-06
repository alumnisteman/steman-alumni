#!/bin/bash
# Script Cleanup Storage STEMAN Alumni
# Lokasi: /var/www/steman-alumni/scripts/cleanup_storage.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

echo "====================================================="
echo "   STEMAN Alumni - Storage Cleanup"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- 1. Clear Laravel Cache ---
echo "[1/5] Membersihkan Laravel Cache..."
docker exec steman-alumni-app-1 php artisan cache:clear
docker exec steman-alumni-app-1 php artisan config:clear
docker exec steman-alumni-app-1 php artisan route:clear
docker exec steman-alumni-app-1 php artisan view:clear
echo "  -> Cache Laravel dibersihkan."

# --- 2. Clear Compiled Views ---
echo "[2/5] Membersihkan Compiled Views..."
rm -rf ./storage/framework/views/*
echo "  -> Compiled views dibersihkan."

# --- 3. Clear Session Files ---
echo "[3/5] Membersihkan Session Files..."
find ./storage/framework/sessions -type f -mtime +1 -delete
echo "  -> Session files lama dibersihkan."

# --- 4. Clear Log Files ---
echo "[4/5] Membersihkan Log Files..."
if [ -f "./storage/logs/laravel.log" ]; then
    echo "" > ./storage/logs/laravel.log
fi
if [ -f "./storage/logs/emergency_fatal.log" ]; then
    echo "" > ./storage/logs/emergency_fatal.log
fi
echo "  -> Log files dibersihkan."

# --- 5. Clear Temporary Files ---
echo "[5/5] Membersihkan Temporary Files..."
find ./storage -name "*.tmp" -type f -delete
find ./storage -name "*.temp" -type f -delete
echo "  -> Temporary files dibersihkan."

# --- Notification ---
if [ ! -z "$TELEGRAM_BOT_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
    curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
        -d chat_id="$TELEGRAM_CHAT_ID" \
        -d text="???? Storage Cleanup Selesai%0A%0AWaktu: $(date)" > /dev/null
fi

echo "====================================================="
echo " Storage Cleanup Selesai!"
echo "====================================================="
