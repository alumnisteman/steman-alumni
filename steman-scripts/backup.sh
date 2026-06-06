#!/bin/bash
# ============================================================
# backup.sh — Backup database harian steman-alumni
# Jalankan via cron setiap hari jam 02:00:
#   0 2 * * * /usr/local/bin/backup.sh
# ============================================================

APP_DIR="/var/www/steman-alumni"
BACKUP_DIR="/backup/steman"
LOG_FILE="/var/log/steman-backup.log"
DB_CONTAINER="steman_db"
DB_NAME="steman_alumni"
DB_ROOT_PASS="${DB_ROOT_PASSWORD}"
KEEP_DAYS=30

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') $1" >> "$LOG_FILE"
}

mkdir -p "$BACKUP_DIR"

TANGGAL=$(date '+%Y-%m-%d_%H-%M')
BACKUP_FILE="$BACKUP_DIR/steman_${TANGGAL}.sql.gz"

log "--- Memulai backup database ---"

docker exec "$DB_CONTAINER" mysqldump \
    -u root \
    -p"${DB_ROOT_PASS}" \
    --single-transaction \
    --routines \
    --triggers \
    "$DB_NAME" \
    | gzip > "$BACKUP_FILE"

if [ $? -eq 0 ] && [ -s "$BACKUP_FILE" ]; then
    SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
    log "SUKSES: Backup tersimpan di $BACKUP_FILE (ukuran: $SIZE)"
else
    log "ERROR: Backup gagal!"
    rm -f "$BACKUP_FILE"
    exit 1
fi

log "Menghapus backup lama (lebih dari $KEEP_DAYS hari)..."
find "$BACKUP_DIR" -name "steman_*.sql.gz" -mtime +$KEEP_DAYS -delete
SISA=$(ls "$BACKUP_DIR" | wc -l)
log "Total backup tersisa: $SISA file"

log "--- Backup selesai ---"
