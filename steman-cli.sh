#!/bin/bash

# STEMAN ALUMNI - UNIFIED MANAGEMENT CLI v1.0
# Created by Antigravity AI

PROJECT_DIR="/var/www/steman-alumni"
APP_CONTAINER="steman_app"
NGINX_CONTAINER="steman_nginx"

cd $PROJECT_DIR

case "$1" in
    update)
        echo "[1/4] Pulling latest code..."
        git pull || echo "Git pull skipped (manually sync files if needed)"
        echo "[2/4] Syncing composer..."
        docker exec $APP_CONTAINER composer install --no-dev --optimize-autoloader
        echo "[3/4] Running migrations..."
        docker exec $APP_CONTAINER php artisan migrate --force
        echo "[4/4] Restarting services..."
        docker compose restart $APP_CONTAINER $NGINX_CONTAINER
        echo "Update complete!"
        ;;
    restart)
        echo "Restarting all containers..."
        docker compose restart
        ;;
    clean)
        echo "Clearing Laravel cache..."
        docker exec $APP_CONTAINER php artisan optimize:clear
        docker exec $APP_CONTAINER php artisan optimize
        echo "Cache cleared!"
        ;;
    logs)
        echo "Viewing application logs (Ctrl+C to exit)..."
        docker logs -f $APP_CONTAINER
        ;;
    ssl-fix)
        echo "Force-checking SSL certificates..."
        docker exec steman_certbot certbot renew
        echo "Reloading Nginx..."
        docker exec $NGINX_CONTAINER nginx -s reload
        echo "SSL check complete!"
        ;;
    db-shell)
        docker exec -it steman_db mariadb -u root -p
        ;;
    *)
        echo "Usage: ./steman-cli.sh {update|restart|clean|logs|ssl-fix|db-shell}"
        exit 1
        ;;
esac
