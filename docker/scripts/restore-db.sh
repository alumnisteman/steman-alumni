#!/bin/bash
# ============================================================
# Script Restore Database - Steman Alumni Portal
# Penggunaan: ./restore-db.sh [nama_file_backup.sql.gz]
# ============================================================

PROJECT_DIR="/opt/steman-alumni"
BACKUP_DIR="/opt/steman-alumni/backups/database"
DB_CONTAINER="steman_db"
DB_NAME="steman_alumni"
DB_USER="app_user"
DB_PASS="strongpassword"
LOG_FILE="/opt/steman-alumni/backups/backup.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# ── Validasi Argumen ─────────────────────────────────────────
if [ -z "$1" ]; then
    echo "Penggunaan: $0 <nama_file_backup.sql.gz>"
    echo ""
    echo "File backup tersedia:"
    ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null || echo "  (Tidak ada file backup)"
    exit 1
fi

RESTORE_FILE="$1"

# Jika hanya nama file (bukan path lengkap), tambahkan direktori backup
if [[ "$RESTORE_FILE" != /* ]]; then
    RESTORE_FILE="$BACKUP_DIR/$RESTORE_FILE"
fi

if [ ! -f "$RESTORE_FILE" ]; then
    log "ERROR: File tidak ditemukan: $RESTORE_FILE"
    exit 1
fi

log "======================================================"
log "  MEMULAI PROSES RESTORE DATABASE"
log "  File: $RESTORE_FILE"
log "======================================================"

# ── Konfirmasi Sebelum Restore ───────────────────────────────
echo ""
echo "⚠️  PERINGATAN: Proses ini akan MENGHAPUS semua data saat ini di database '$DB_NAME'!"
echo "    File restore: $RESTORE_FILE"
echo ""
read -p "Ketik 'YA' untuk melanjutkan: " CONFIRM
if [ "$CONFIRM" != "YA" ]; then
    log "Restore dibatalkan oleh pengguna."
    exit 0
fi

# ── Cek Container ────────────────────────────────────────────
if ! docker ps --format '{{.Names}}' | grep -q "^${DB_CONTAINER}$"; then
    log "ERROR: Container '$DB_CONTAINER' tidak berjalan."
    exit 1
fi

# ── Jalankan Restore ─────────────────────────────────────────
log "Menjalankan restore dari file terkompresi..."
gunzip < "$RESTORE_FILE" | docker exec -i "$DB_CONTAINER" \
    mysql \
    --user="$DB_USER" \
    --password="$DB_PASS" \
    "$DB_NAME"

if [ $? -eq 0 ]; then
    log "✅ SUKSES: Database berhasil di-restore dari $RESTORE_FILE"
else
    log "❌ GAGAL: Proses restore tidak berhasil."
    exit 1
fi

log "======================================================"
log "  RESTORE SELESAI"
log "======================================================"
exit 0
