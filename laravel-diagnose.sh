#!/bin/bash

REPORT="laravel_diagnose_$(date +%Y%m%d_%H%M%S).txt"

{
  echo "================================================="
  echo "LARAVEL FULL DIAGNOSTIC REPORT"
  echo "================================================="
  echo

  echo "===== SERVER INFO ====="
  uname -a
  uptime
  echo

  echo "===== CPU & MEMORY ====="
  free -h
  top -bn1 | head -20
  echo

  echo "===== DISK ====="
  df -h
  echo

  echo "===== PHP ====="
  php -v
  php -m
  echo

  echo "===== LARAVEL INFO ====="
  php artisan about 2>/dev/null
  echo

  echo "===== ROUTES ====="
  php artisan route:list 2>/dev/null
  echo

  echo "===== QUEUE FAILED ====="
  php artisan queue:failed 2>/dev/null
  echo

  echo "===== MIGRATION STATUS ====="
  php artisan migrate:status 2>/dev/null
  echo

  echo "===== COMPOSER AUDIT ====="
  composer audit 2>/dev/null
  echo

  echo "===== NGINX ERRORS ====="
  tail -100 /var/log/nginx/error.log 2>/dev/null
  echo

  echo "===== PHP-FPM ERRORS ====="
  find /var/log -name "*php*fpm*.log" -exec tail -50 {} \; 2>/dev/null
  echo

  echo "===== LARAVEL LOG ====="
  tail -200 storage/logs/laravel.log 2>/dev/null
  echo

  echo "===== MYSQL PROCESS ====="
  mysql -e "SHOW PROCESSLIST;" 2>/dev/null
  echo

  echo "===== REDIS INFO ====="
  redis-cli info 2>/dev/null | grep -E "used_memory_human|connected_clients|uptime_in_days"
  echo

  echo "===== STORAGE PERMISSIONS ====="
  ls -ld storage bootstrap/cache 2>/dev/null
  echo

  echo "===== LARGE FILES ====="
  find . -type f -size +50M 2>/dev/null
  echo

  echo "===== ENV CHECK ====="
  grep -E "APP_ENV|APP_DEBUG|DB_CONNECTION|CACHE_DRIVER|QUEUE_CONNECTION" .env 2>/dev/null
  echo

  echo "===== INSTALLED PACKAGES ====="
  composer show --direct 2>/dev/null
  echo

  echo "===== LAST 50 ERROR LINES ====="
  grep -Ri "error\|exception\|fatal" storage/logs 2>/dev/null | tail -50
  echo

  echo "================================================="
  echo "END OF REPORT"
  echo "================================================="
} > "$REPORT"

echo "Report generated: $REPORT"
