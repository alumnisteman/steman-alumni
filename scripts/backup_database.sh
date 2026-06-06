#!/bin/bash
# Script Backup Database STEMAN Alumni
# Lokasi: /var/www/steman-alumni/scripts/backup_database.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# --- Configuration ---
BACKUP_DIR="./backups/database"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="steman_alumni_${TIMESTAMP}.sql"
RETENTION_DAYS=7

# --- Create Backup Directory ---
mkdir -p "$BACKUP_DIR"

echo "====================================================="
echo "   STEMAN Alumni - Database Backup"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- Backup Database ---
echo "[1/2] Melakukan backup database..."
docker exec steman_db mysqldump -uroot -pCh4v4run3@ steman_alumni > "$BACKUP_DIR/$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "  -> Backup berhasil: $BACKUP_DIR/$BACKUP_FILE"
    
    # --- Compress Backup ---
    echo "[2/2] Mengompres backup..."
    gzip "$BACKUP_DIR/$BACKUP_FILE"
    echo "  -> Backup terkompres: $BACKUP_DIR/${BACKUP_FILE}.gz"
    
    # --- Clean Old Backups ---
    echo "   Membersihkan backup lama (lebih dari $RETENTION_DAYS hari)..."
    find "$BACKUP_DIR" -name "*.sql.gz" -type f -mtime +$RETENTION_DAYS -delete
    echo "  -> Backup lama telah dibersihkan."
    
    # --- Notification ---
    if [ ! -z "$TELEGRAM_BOT_TOKEN" ] && [ ! -z "$TELEGRAM_CHAT_ID" ]; then
        curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
            -d chat_id="$TELEGRAM_CHAT_ID" \
            -d text="???? Database Backup Selesai%0A%0AFile: ${BACKUP_FILE}.gz%0AWaktu: $(date)" > /dev/null
    fi
    
    echo "====================================================="
    echo " Backup Database Selesai!"
    echo "====================================================="
else
    echo "  -> ERROR: Backup gagal!"
    exit 1
fi
