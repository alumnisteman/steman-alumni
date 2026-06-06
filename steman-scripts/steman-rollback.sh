#!/bin/bash
# ============================================================
# steman-rollback.sh — Auto Rollback ke versi sehat terakhir
# Dipanggil otomatis oleh steman-heal.sh saat repair gagal
# atau bisa dijalankan manual: /usr/local/bin/steman-rollback.sh
# ============================================================

RELEASES_DIR="/var/www/releases"
CURRENT_LINK="/var/www/current"
APP_DIR="/var/www/steman-alumni"
LOG_FILE="/var/log/steman-heal.log"

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') ROLLBACK: $1" >> "$LOG_FILE"
}

log "--- Memulai proses rollback ---"

if [ ! -d "$RELEASES_DIR" ]; then
    log "ERROR: Direktori releases tidak ditemukan di $RELEASES_DIR"
    exit 1
fi

RELEASES=($(ls -1t "$RELEASES_DIR"))
TOTAL=${#RELEASES[@]}

if [ "$TOTAL" -lt 2 ]; then
    log "ERROR: Tidak ada versi sebelumnya untuk rollback (total rilis: $TOTAL)"
    exit 1
fi

CURRENT_RELEASE=$(readlink "$CURRENT_LINK" 2>/dev/null | xargs basename)
log "Rilis aktif saat ini: $CURRENT_RELEASE"

TARGET_RELEASE=""
for release in "${RELEASES[@]}"; do
    if [ "$release" != "$CURRENT_RELEASE" ]; then
        TARGET_RELEASE="$release"
        break
    fi
done

if [ -z "$TARGET_RELEASE" ]; then
    log "ERROR: Tidak ditemukan rilis target untuk rollback"
    exit 1
fi

log "Rollback ke: $TARGET_RELEASE"

ln -sfn "$RELEASES_DIR/$TARGET_RELEASE" "$CURRENT_LINK"

cd "$RELEASES_DIR/$TARGET_RELEASE" || { log "ERROR: Gagal masuk ke direktori rilis"; exit 1; }

docker compose restart

sleep 30

APP_STATUS=$(docker inspect --format='{{.State.Health.Status}}' steman_app 2>/dev/null)

if [ "$APP_STATUS" = "healthy" ]; then
    log "SUKSES: Rollback ke $TARGET_RELEASE berhasil. Sistem sehat."
else
    log "KRITIS: Rollback gagal! Status: $APP_STATUS. Perlu intervensi manual."
    exit 1
fi

log "--- Rollback selesai ---"
