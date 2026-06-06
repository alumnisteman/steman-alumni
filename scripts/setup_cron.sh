#!/bin/bash
# Script Setup Cron Jobs untuk STEMAN Alumni
# Lokasi: /var/www/steman-alumni/scripts/setup_cron.sh

# --- Auto Detect Project Root ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

echo "====================================================="
echo "   STEMAN Alumni - Setup Cron Jobs"
echo "   Waktu      : $(date)"
echo "   Project Dir: $PROJECT_ROOT"
echo "====================================================="

# --- Add Cron Jobs ---
echo "[1/4] Setup Maintenance Cron (Daily at 3 AM)..."
(crontab -l 2>/dev/null; echo "0 3 * * * cd $PROJECT_ROOT && ./scripts/maintenance.sh >> ./logs/maintenance.log 2>&1") | crontab -
echo "  -> Maintenance cron added."

echo "[2/4] Setup Backup Cron (Daily at 2 AM)..."
(crontab -l 2>/dev/null; echo "0 2 * * * cd $PROJECT_ROOT && ./scripts/backup_database.sh >> ./logs/backup.log 2>&1") | crontab -
echo "  -> Backup cron added."

echo "[3/6] Setup Error Monitoring Cron (Every 6 hours)..."
(crontab -l 2>/dev/null; echo "0 */6 * * * cd $PROJECT_ROOT && ./scripts/monitor_errors.sh >> ./logs/monitor.log 2>&1") | crontab -
echo "  -> Error monitoring cron added."

echo "[4/6] Setup SSL Certificate Automation Cron (Daily at 1 AM)..."
(crontab -l 2>/dev/null; echo "0 1 * * * cd $PROJECT_ROOT && ./scripts/ssl_automation.sh >> ./logs/ssl.log 2>&1") | crontab -
echo "  -> SSL automation cron added."

echo "[5/6] Setup Health Check Cron (Every 5 minutes)..."
(crontab -l 2>/dev/null; echo "*/5 * * * * cd $PROJECT_ROOT && ./scripts/health_check.sh >> ./logs/health.log 2>&1") | crontab -
echo "  -> Health check cron added."

echo "[6/7] Setup Storage Cleanup Cron (Weekly on Sunday at 4 AM)..."
(crontab -l 2>/dev/null; echo "0 4 * * 0 cd $PROJECT_ROOT && ./scripts/cleanup_storage.sh >> ./logs/cleanup.log 2>&1") | crontab -
echo "  -> Storage cleanup cron added."

echo "[7/7] Setup Site URL Monitor (Every 15 minutes)..."
(crontab -l 2>/dev/null; echo "*/15 * * * * cd $PROJECT_ROOT && ./scripts/monitor_site.sh >> ./logs/monitor-site.log 2>&1") | crontab -
echo "  -> Site URL monitor added."

# --- Create Log Directory ---
mkdir -p ./logs

echo "====================================================="
echo " Cron Jobs Setup Selesai!"
echo "====================================================="
echo "Daftar Cron Jobs:"
crontab -l
