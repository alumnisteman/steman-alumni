#!/bin/bash
# Script Backup Automaits STEMAN Alumni v5 - Robust Version
# Lokasi: /var/www/steman-alumni/scripts/backup.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# --- Konfigurasi ---
BACKUP_DIR="$PROJECT_ROOT/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RETENTION_DAYS=7

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

# Fallback (If .env loading failed)
DB_NAME=${DB_NAME:-steman_alumni}
DB_USER=${DB_USER:-steman}
DB_PASS=${DB_PASS:-M4ruw4h3}

mkdir -p "$BACKUP_DIR"

echo "====================================================="
echo "   STEMAN Alumni v5 - Automated Backup System"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- 1. Database Backup ---
echo "[1/2] Mencadangkan Database ($DB_NAME) dari container steman_db..."
# Use steman_db as HOST inside docker, but here we run docker exec
docker exec steman_db mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql" 2>/dev/null

if [ -s "$BACKUP_DIR/db_backup_$TIMESTAMP.sql" ]; then
    gzip "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
    echo "  -> Database berhasil dicadangkan: db_backup_$TIMESTAMP.sql.gz"
else
    echo "  -> GAGAL: Database tidak dapat dicadangkan atau kosong!"
    notify "Backup Database" "ERROR" "Gagal mencadangkan database $DB_NAME. File kosong atau error koneksi."
    rm -f "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
fi

# --- 2. Storage File Backup ---
echo "[2/2] Mencadangkan File Storage (Uploadan Alumni)..."
if [ -d "./storage/app/public" ]; then
    tar -czf "$BACKUP_DIR/storage_backup_$TIMESTAMP.tar.gz" -C "./storage/app" public
    echo "  -> File Storage berhasil dicadangkan: storage_backup_$TIMESTAMP.tar.gz"
    notify "Backup Mingguan" "SUCCESS" "Database ($DB_NAME) dan Storage berhasil dicadangkan ke $BACKUP_DIR."
else
    echo "  -> GAGAL: Folder storage/app/public tidak ditemukan!"
    notify "Backup Storage" "WARNING" "Database berhasil namun folder storage/app/public tidak ditemukan."
fi

# --- 3. Cleanup Old Backups ---
echo "[3/3] Membersihkan cadangan lama (lebih dari $RETENTION_DAYS hari)..."
find "$BACKUP_DIR" -type f -mtime +$RETENTION_DAYS -name "*.gz" -exec rm {} \;
echo "  -> Pembersihan selesai."

echo "====================================================="
echo " Cadangan Selesai disimpan di: $BACKUP_DIR"
echo "====================================================="
