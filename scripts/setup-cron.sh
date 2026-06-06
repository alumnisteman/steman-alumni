#!/usr/bin/env bash

# Setup Cron Jobs for Steman Alumni
# Run this script on the host server

echo "Setting up Cron Jobs for Steman Alumni..."

CRON_FILE="/tmp/steman_cron"
crontab -l > "$CRON_FILE" 2>/dev/null || true

# Add Backup Job (Run at 02:00 AM every day)
if ! grep -q "backup-db.sh" "$CRON_FILE"; then
  echo "0 2 * * * /bin/bash /var/www/scripts/backup-db.sh >> /var/log/steman-backup.log 2>&1" >> "$CRON_FILE"
  echo "Added backup-db.sh to cron."
else
  echo "backup-db.sh already in cron."
fi

# Add Cache Clear Job (Run at 03:00 AM every day)
if ! grep -q "artisan cache:clear" "$CRON_FILE"; then
  echo "0 3 * * * docker exec -i steman_app php artisan cache:clear >> /var/log/steman-cache.log 2>&1" >> "$CRON_FILE"
  echo "Added cache:clear to cron."
else
  echo "cache:clear already in cron."
fi

crontab "$CRON_FILE"
rm -f "$CRON_FILE"

echo "Cron setup complete. Current crontab:"
crontab -l
