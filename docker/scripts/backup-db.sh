#!/bin/bash
# ============================================================
# Script Backup Database Otomatis - Steman Alumni Portal
# Versi: 1.1 | Diupdate: April 2026
# ============================================================

# ── Konfigurasi ──────────────────────────────────────────────
PROJECT_DIR="/var/www/steman-alumni"
BACKUP_DIR="$PROJECT_DIR/backups/database"
COMPOSE_FILE="docker-compose.prod.yml"
DB_CONTAINER="steman_db"
RETENTION_DAYS=30   # Hapus backup yang lebih lama dari N hari
LOG_FILE="$PROJECT_DIR/backups/backup.log"

# ── Fungsi Log ───────────────────────────────────────────────
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# ── Persiapan ────────────────────────────────────────────────
if [ ! -d "$PROJECT_DIR" ]; then
    echo "ERROR: Folder project tidak ditemukan: $PROJECT_DIR"
    exit 1
fi

mkdir -p "$BACKUP_DIR"
touch "$LOG_FILE"
cd "$PROJECT_DIR" || exit 1

# Load Database Credentials from .env
if [ -f .env ]; then
    DB_NAME=$(grep '^DB_DATABASE=' .env | cut -d '=' -f2)
    DB_USER=$(grep '^DB_USERNAME=' .env | cut -d '=' -f2)
    DB_PASS=$(grep '^DB_PASSWORD=' .env | cut -d '=' -f2)
else
    log "ERROR: File .env tidak ditemukan di $PROJECT_DIR"
    exit 1
fi

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
log "Daftar backup tersedia saat ini (3 terakhir):"
ls -lh "$BACKUP_DIR"/*.sql.gz 2>/dev/null | tail -n 3 | awk '{print "  " $9 " (" $5 ")"}' | tee -a "$LOG_FILE"

log "======================================================"
log "  BACKUP SELESAI"
log "======================================================"

exit 0
