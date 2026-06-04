#!/usr/bin/env bash
set -euo pipefail

# 1. Hapus file log Laravel lama (>30 hari)
find /var/www/steman-alumni/storage/logs -type f -name "*.log" -mtime +30 -delete

# 2. Hapus file temporary di /tmp lama (>7 hari)
find /tmp -type f -mtime +7 -delete

# 3. Bersihkan Docker (gambar, container, network, volume yang tidak terpakai > 24 jam)
docker system prune -af --filter "until=24h"

docker volume prune -f

# 4. Optimasi tabel database MySQL/MariaDB
mysqlcheck --all-databases --auto-repair --optimize

# 5. Kirim notifikasi Telegram (pastikan token & chat_id di environment)
if command -v curl > /dev/null; then
  if [[ -n "${TELEGRAM_BOT_TOKEN:-}" && -n "${TELEGRAM_CHAT_ID:-}" ]]; then
    curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
      -d chat_id="${TELEGRAM_CHAT_ID}" \
      -d parse_mode="Markdown" \
      -d text="*Maintenance*\nRoutine maintenance completed successfully."
  fi
fi
