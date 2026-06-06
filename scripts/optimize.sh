#!/bin/bash
# ==============================================================
# STEMAN ALUMNI - ONE-CLICK OPTIMIZATION SCRIPT
# Clears all caches and rebuilds them for production stability.
# ==============================================================

CONTAINER="steman-alumni-app-1"

echo "============================================"
echo " STEMAN ALUMNI - SYSTEM OPTIMIZATION"
echo "============================================"

# 1. Clear everything first
echo "[1/2] Clearing all caches..."
docker exec $CONTAINER php artisan optimize:clear
docker exec $CONTAINER php artisan view:clear

# 2. Rebuild caches for performance
echo "[2/2] Rebuilding caches..."
docker exec $CONTAINER php artisan config:cache
docker exec $CONTAINER php artisan route:cache
docker exec $CONTAINER php artisan view:cache
docker exec $CONTAINER php artisan event:cache

echo "============================================"
echo " OPTIMIZATION COMPLETE!"
echo "============================================"
