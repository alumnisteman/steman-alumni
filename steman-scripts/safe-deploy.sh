#!/bin/bash
# Safe Deploy Script - Alumni STEMAN
# Jalankan script ini setiap kali ada update kode ke server

set -e
APP_DIR="/var/www/steman-alumni"
DATE=$(date "+%Y-%m-%d %H:%M:%S")
echo "[$DATE] === Memulai safe deploy ==="
cd "$APP_DIR"

echo "[1/6] Regenerate composer autoload..."
composer dump-autoload --optimize --no-dev

echo "[2/6] Bersihkan package dev dari cache..."
php -r "
\$file = \"bootstrap/cache/packages.php\";
if (file_exists(\$file)) {
    \$data = include \$file;
    \$dev_packages = [\"nunomaduro/collision\", \"pestphp/pest\"];
    foreach (\$dev_packages as \$pkg) { unset(\$data[\$pkg]); }
    file_put_contents(\$file, \"<?php return \" . var_export(\$data, true) . \";\");
    echo \"packages.php dibersihkan\n\";
}
"
rm -f bootstrap/cache/services.php

echo "[3/6] Clear cache Laravel di container..."
docker exec steman_app php artisan cache:clear
docker exec steman_app php artisan view:clear
docker exec steman_app php artisan config:clear
docker exec steman_app php artisan route:clear

echo "[4/6] Re-cache untuk production..."
docker exec steman_app php artisan config:cache
docker exec steman_app php artisan route:cache

echo "[5/6] Restart containers..."
docker restart steman_app steman_queue
sleep 10

echo "[6/6] Verifikasi website..."
HTTP=$(curl -s -o /dev/null -w "%{http_code}" --max-time 15 https://alumni-steman.my.id/)
if [ "$HTTP" = "200" ]; then
    echo "SUCCESS: Website OK (HTTP 200)"
else
    echo "WARNING: Website HTTP $HTTP - cek manual!"
fi
echo "[$DATE] Deploy selesai."
