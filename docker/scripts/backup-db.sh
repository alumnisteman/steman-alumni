#!/bin/bash
# ============================================================
# Script Backup Database Otomatis - Steman Alumni Portal
# Versi: 1.0 | Diupdate: April 2026
# ============================================================

# ── Konfigurasi ──────────────────────────────────────────────
PROJECT_DIR="/opt/steman-alumni"
BACKUP_DIR="/opt/steman-alumni/backups/database"
COMPOSE_FILE="docker-compose.prod.yml"
DB_CONTAINER="steman_db"
DB_NAME="steman_alumni"
DB_USER="app_user"
DB_PASS="strongpassword"
RETENTION_DAYS=30   # Hapus backup yang lebih lama dari N hari
LOG_FILE="/opt/steman-alumni/backups/backup.log"

# ── Fungsi Log ───────────────────────────────────────────────
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# ── Persiapan ────────────────────────────────────────────────
mkdir -p "$BACKUP_DIR"
touch "$LOG_FILE"
cd "$PROJECT_DIR" || { log "ERROR: Folder project tidak ditemukan: $PROJECT_DIR"; exit 1; }

log "======================================================"
log "  MEMULAI PROSES BACKUP DATABASE: $DB_NAME"
log "======================================================"

# ── Cek Container Database Berjalan ─────────────────────────
if ! docker ps --format '{{.Names}}' | grep -q "^${DB_CONTAINER}$"; then
    log "ERROR: Container '$DB_CONTAINER' tidak berjalan. Backup dibatalkan."
    exit 1
fi

# ── Nama File Backup ─────────────────────────────────────────
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
BACKUP_FILE="$BACKUP_DIR/steman_alumni_${TIMESTAMP}.sql.gz"

# ── Jalankan mysqldump via Docker ────────────────────────────
log "Menjalankan mysqldump..."
docker exec "$DB_CONTAINER" \
    mysqldump \
    --user="$DB_USER" \
    --password="$DB_PASS" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    "$DB_NAME" | gzip > "$BACKUP_FILE"

# ── Validasi Hasil Backup ────────────────────────────────────
if [ $? -eq 0 ] && [ -s "$BACKUP_FILE" ]; then
    BACKUP_SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
    log "✅ SUKSES: Backup tersimpan di $BACKUP_FILE (Ukuran: $BACKUP_SIZE)"
else
    log "❌ GAGAL: Backup database tidak berhasil dibuat."
    rm -f "$BACKUP_FILE"
    exit 1
fi

# ── Hapus Backup Lama (Lebih dari RETENTION_DAYS hari) ───────
log "Membersihkan backup lama (lebih dari $RETENTION_DAYS hari)..."
DELETED_COUNT=$(find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -print -delete | wc -l)
log "Total backup lama yang dihapus: $DELETED_COUNT file."

# ── Tampilkan Daftar Backup yang Ada ─────────────────────────
log "Daftar backup tersedia saat ini:"
ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null | awk '{print "  " $9 " (" $5 ")"}' | tee -a "$LOG_FILE"

log "======================================================"
log "  BACKUP SELESAI"
log "======================================================"

exit 0
