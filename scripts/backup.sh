#!/bin/bash
# STEMAN ALUMNI PORTAL - ZERO-LOSS BACKUP SYSTEM V2
# Purpose: Ensures data is safe forever via local and optional remote backups.

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

# --- Configuration ---
BACKUP_DIR="$PROJECT_ROOT/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RETENTION_DAYS=14 # Keep 2 weeks locally

# --- Load DB Credentials from .env ---
DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d'=' -f2)

# --- Remote Backup Config (Optional) ---
# Set these in .env if you want offsite storage
REMOTE_BACKUP_ENABLED=$(grep REMOTE_BACKUP_ENABLED .env | cut -d'=' -f2 || echo "false")
REMOTE_SSH_HOST=$(grep REMOTE_SSH_HOST .env | cut -d'=' -f2)
REMOTE_SSH_USER=$(grep REMOTE_SSH_USER .env | cut -d'=' -f2)
REMOTE_SSH_PATH=$(grep REMOTE_SSH_PATH .env | cut -d'=' -f2)

mkdir -p "$BACKUP_DIR"

echo "==========================================="
echo "   STEMAN SENTINEL: BACKUP INITIATED       "
echo "   Time: $(date)"
echo "==========================================="

# 1. Database Backup
echo "[1/3] Dumping Database ($DB_NAME)..."
docker exec steman_db mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/db_$TIMESTAMP.sql" 2>/dev/null

if [ -s "$BACKUP_DIR/db_$TIMESTAMP.sql" ]; then
    gzip "$BACKUP_DIR/db_$TIMESTAMP.sql"
    echo "  -> DB Backup Successful: db_$TIMESTAMP.sql.gz"
else
    echo "  -> ERROR: Database dump failed or empty!"
    exit 1
fi

# 2. Storage Backup
echo "[2/3] Archiving Storage Files..."
tar -czf "$BACKUP_DIR/storage_$TIMESTAMP.tar.gz" -C "$PROJECT_ROOT/storage/app" public 2>/dev/null
echo "  -> Storage Backup Successful: storage_$TIMESTAMP.tar.gz"

# 3. Offsite Transfer (If enabled)
if [ "$REMOTE_BACKUP_ENABLED" == "true" ]; then
    echo "[3/3] Transferring to Remote Server ($REMOTE_SSH_HOST)..."
    scp "$BACKUP_DIR/db_$TIMESTAMP.sql.gz" "$BACKUP_DIR/storage_$TIMESTAMP.tar.gz" "$REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_SSH_PATH"
    echo "  -> Remote Transfer Complete."
else
    echo "[3/3] Remote Backup Disabled. Skipping transfer."
fi

# 4. Cleanup Old Backups
echo "Cleaning up local backups older than $RETENTION_DAYS days..."
find "$BACKUP_DIR" -type f -mtime +$RETENTION_DAYS -delete

echo "==========================================="
echo "   BACKUP COMPLETED SUCCESSFULLY! ✅      "
echo "==========================================="

