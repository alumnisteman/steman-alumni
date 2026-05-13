#!/bin/bash
# scripts/setup-production-domain.sh - Setup domain and SSL on production

DOMAIN="alumni-steman.my.id"
EMAIL="admin@steman-alumni.com" # Replace with your email

echo "--- 1. Pulling Latest Changes ---"
git pull origin main

echo "--- 2. Updating Environment ---"
# Ensure the .env on server matches the pushed .env (or update manually)
# We already pushed the .env changes, so git pull should handle it if not gitignored.
# Note: Usually .env is gitignored, so we might need to update it manually if it is.

echo "--- 3. Stopping Nginx for SSL initialization ---"
docker compose -f docker-compose.prod.yml stop webserver

echo "--- 4. Generating Let's Encrypt SSL ---"
# Using certbot in standalone mode to get the first certificate
docker run --rm -it --name certbot \
    -v "/var/www/steman-alumni/certbot_certs:/etc/letsencrypt" \
    -v "/var/www/steman-alumni/certbot_www:/var/www/certbot" \
    certbot/certbot certonly --manual --preferred-challenges dns -d $DOMAIN

# NOTE: If DNS challenge is too complex, we can use webroot after starting nginx with a placeholder.
# But since Nginx is currently failing (Bad Gateway), standalone or manual is safer for the first run.

echo "--- 5. Restarting all services ---"
docker compose -f docker-compose.prod.yml up -d --build --remove-orphans

echo "--- 6. Running Post-Fixes ---"
bash scripts/db/final_prod_fix.sh

echo "--- SUCCESS! Domain $DOMAIN should be live shortly ---"
