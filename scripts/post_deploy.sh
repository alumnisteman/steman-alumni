#!/usr/bin/env bash
set -e

# Path to docker compose file
COMPOSE_FILE="/var/www/steman-alumni/docker-compose.prod.yml"

# Run migrations
docker compose -f "$COMPOSE_FILE" exec app php artisan migrate --force

# Clear caches
docker compose -f "$COMPOSE_FILE" exec app php artisan config:clear
docker compose -f "$COMPOSE_FILE" exec app php artisan route:clear
docker compose -f "$COMPOSE_FILE" exec app php artisan view:clear
docker compose -f "$COMPOSE_FILE" exec app php artisan cache:clear

# Clean old logs (daily task, can also be run here)
docker compose -f "$COMPOSE_FILE" exec app php artisan logs:clean

# Restart web services to apply changes
docker compose -f "$COMPOSE_FILE" restart nginx php

# Run health check once to verify everything is up
docker compose -f "$COMPOSE_FILE" exec app php artisan system:health

echo "Post-deploy tasks completed successfully."
