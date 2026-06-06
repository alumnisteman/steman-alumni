#!/bin/bash
# scripts/self-heal.sh - Automated recovery for Steman Alumni Portal

URL="https://alumni-steman.my.id/feed"
LOG_FILE="/var/log/steman_self_heal.log"

check_health() {
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$URL")
    echo "$(date): Health check $URL -> $HTTP_CODE" >> "$LOG_FILE"
    
    if [[ "$HTTP_CODE" == "502" || "$HTTP_CODE" == "503" || "$HTTP_CODE" == "504" ]]; then
        echo "$(date): CRITICAL - detected $HTTP_CODE. Restarting Nginx and checking App..." >> "$LOG_FILE"
        docker restart steman_nginx
        sleep 2
        # Check if App is running
        if ! docker ps | grep -q steman-alumni-app; then
            echo "$(date): App container missing. Restarting stack..." >> "$LOG_FILE"
            cd /var/www/steman-alumni && docker compose -f docker-compose.prod.yml up -d
        fi
    elif [[ "$HTTP_CODE" == "500" ]]; then
        echo "$(date): ERROR - detected 500. Clearing Laravel cache..." >> "$LOG_FILE"
        docker exec steman-alumni-app-1 php artisan cache:clear
        docker exec steman-alumni-app-1 php artisan view:clear
        docker exec steman-alumni-app-1 php artisan config:cache
    fi
}

# Ensure log file exists
touch "$LOG_FILE"
check_health
