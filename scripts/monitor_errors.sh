#!/bin/bash
# Script Monitoring Error STEMAN Alumni
# Lokasi: /var/www/steman-alumni/scripts/monitor_errors.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# --- Configuration ---
LOG_FILE="./storage/logs/laravel.log"
EMERGENCY_LOG="./storage/logs/emergency_fatal.log"
ERROR_THRESHOLD=10

echo "====================================================="
echo "   STEMAN Alumni - Error Monitoring"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- Check Laravel Log for Errors ---
echo "[1/2] Memeriksa Laravel Log..."
if [ -f "$LOG_FILE" ]; then
    ERROR_COUNT=$(grep -c "ERROR" "$LOG_FILE" || echo 0)
    echo "  -> Total ERROR ditemukan: $ERROR_COUNT"
    
    if [ $ERROR_COUNT -gt $ERROR_THRESHOLD ]; then
        echo "  -> WARNING: Jumlah error melebihi threshold ($ERROR_THRESHOLD)"
        
        # Get last 5 errors
        echo "  -> 5 error terakhir:"
        grep "ERROR" "$LOG_FILE" | tail -5
        
        # Send notification
        if [ ! -z "$TELEGRAM_BOT_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
            LAST_ERROR=$(grep "ERROR" "$LOG_FILE" | tail -1)
            curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
                -d chat_id="$TELEGRAM_CHAT_ID" \
                -d text="?????? ERROR ALERT%0A%0ATotal Error: $ERROR_COUNT%0ALast Error: $LAST_ERROR" > /dev/null
        fi
    else
        echo "  -> OK: Jumlah error dalam batas normal"
    fi
else
    echo "  -> Log file tidak ditemukan"
fi

# --- Check Emergency Log ---
echo "[2/2] Memeriksa Emergency Log..."
if [ -f "$EMERGENCY_LOG" ]; then
    EMERGENCY_COUNT=$(wc -l < "$EMERGENCY_LOG")
    echo "  -> Total baris di emergency log: $EMERGENCY_COUNT"
    
    if [ $EMERGENCY_COUNT -gt 0 ]; then
        echo "  -> WARNING: Ada error fatal di emergency log"
        
        # Send notification
        if [ ! -z "$TELEGRAM_BOT_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
            curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
                -d chat_id="$TELEGRAM_CHAT_ID" \
                -d text="???? FATAL ERROR ALERT%0A%0ATerjadi error fatal di emergency_fatal.log%0AJumlah: $EMERGENCY_COUNT baris" > /dev/null
        fi
    else
        echo "  -> OK: Tidak ada error fatal"
    fi
else
    echo "  -> Emergency log tidak ditemukan"
fi

echo "====================================================="
echo " Monitoring Selesai!"
echo "====================================================="
