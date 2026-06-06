#!/bin/bash
# ============================================================
# setup-cron.sh — Pasang semua script dan cron otomatis
# Jalankan SEKALI di server sebagai root:
#   bash setup-cron.sh
# ============================================================

echo "=== Setup Self-Healing Infrastructure steman-alumni ==="

echo "[1/5] Menyalin script ke /usr/local/bin/..."
cp steman-heal.sh /usr/local/bin/steman-heal.sh
cp steman-rollback.sh /usr/local/bin/steman-rollback.sh
cp backup.sh /usr/local/bin/backup.sh

echo "[2/5] Memberikan izin eksekusi..."
chmod +x /usr/local/bin/steman-heal.sh
chmod +x /usr/local/bin/steman-rollback.sh
chmod +x /usr/local/bin/backup.sh

echo "[3/5] Membuat direktori log dan backup..."
mkdir -p /var/log
mkdir -p /backup/steman
touch /var/log/steman-heal.log
touch /var/log/steman-backup.log

echo "[4/5] Memasang cron jobs..."
CRON_JOBS="*/5 * * * * /usr/local/bin/steman-heal.sh >> /var/log/steman-heal.log 2>&1
0 2 * * * /usr/local/bin/backup.sh >> /var/log/steman-backup.log 2>&1"

(crontab -l 2>/dev/null | grep -v "steman"; echo "$CRON_JOBS") | crontab -

echo "[5/5] Verifikasi cron terpasang:"
crontab -l | grep steman

echo ""
echo "=== SELESAI! ==="
echo ""
echo "Cron jobs aktif:"
echo "  - steman-heal.sh  : setiap 5 menit (auto-repair)"
echo "  - backup.sh       : setiap hari jam 02:00 (backup DB)"
echo ""
echo "Log files:"
echo "  - /var/log/steman-heal.log"
echo "  - /var/log/steman-backup.log"
echo ""
echo "Test manual:"
echo "  /usr/local/bin/steman-heal.sh"
echo "  /usr/local/bin/backup.sh"
