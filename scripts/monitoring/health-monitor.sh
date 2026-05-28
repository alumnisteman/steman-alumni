#!/bin/bash

# Health Monitoring Script for Steman Alumni
# Monitors application health and sends alerts via Telegram

set -e

# Configuration
APP_URL="https://alumni-steman.my.id"
TELEGRAM_BOT_TOKEN="${TELEGRAM_BOT_TOKEN:-}"
TELEGRAM_CHAT_ID="${TELEGRAM_CHAT_ID:-}"
LOG_FILE="/var/www/steman-alumni/storage/logs/health-monitor.log"
CHECK_INTERVAL=300  # 5 minutes

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Logging
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Telegram notification
send_telegram() {
    local message="$1"
    if [ -n "$TELEGRAM_BOT_TOKEN" ] && [ -n "$TELEGRAM_CHAT_ID" ]; then
        curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
            -d "chat_id=$TELEGRAM_CHAT_ID" \
            -d "text=$message" \
            -d "parse_mode=HTML" > /dev/null 2>&1
    fi
}

# Check HTTP status
check_http_status() {
    local response=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" --max-time 10)
    if [ "$response" -eq 200 ]; then
        log "${GREEN}HTTP Status: $response${NC}"
        return 0
    else
        log "${RED}HTTP Status: $response${NC}"
        return 1
    fi
}

# Check response time
check_response_time() {
    local time=$(curl -o /dev/null -s -w "%{time_total}" "$APP_URL" --max-time 10)
    local time_ms=$(echo "$time * 1000" | bc)
    
    if (( $(echo "$time < 2.0" | bc -l) )); then
        log "${GREEN}Response Time: ${time_ms}ms${NC}"
        return 0
    else
        log "${YELLOW}Response Time: ${time_ms}ms (slow)${NC}"
        return 1
    fi
}

# Check SSL certificate
check_ssl() {
    local expiry=$(echo | openssl s_client -servername alumni-steman.my.id -connect alumni-steman.my.id:443 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2)
    local expiry_date=$(date -d "$expiry" +%s)
    local current_date=$(date +%s)
    local days_left=$(( ($expiry_date - $current_date) / 86400 ))
    
    if [ "$days_left" -gt 30 ]; then
        log "${GREEN}SSL Certificate: Valid for $days_left days${NC}"
        return 0
    else
        log "${RED}SSL Certificate: Expires in $days_left days${NC}"
        send_telegram "⚠️ SSL Certificate expires in $days_left days"
        return 1
    fi
}

# Check disk space
check_disk_space() {
    local usage=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    
    if [ "$usage" -lt 80 ]; then
        log "${GREEN}Disk Usage: ${usage}%${NC}"
        return 0
    else
        log "${RED}Disk Usage: ${usage}%${NC}"
        send_telegram "⚠️ Disk usage at ${usage}%"
        return 1
    fi
}

# Check Docker containers
check_docker_containers() {
    local unhealthy=$(docker ps --filter "health=unhealthy" --format "{{.Names}}" | wc -l)
    
    if [ "$unhealthy" -eq 0 ]; then
        log "${GREEN}All Docker containers healthy${NC}"
        return 0
    else
        log "${RED}$unhealthy Docker containers unhealthy${NC}"
        send_telegram "❌ $unhealthy Docker containers unhealthy"
        return 1
    fi
}

# Check database connection
check_database() {
    if docker exec steman_db mysqladmin ping -h localhost -u root -p"${DB_PASSWORD}" > /dev/null 2>&1; then
        log "${GREEN}Database connection: OK${NC}"
        return 0
    else
        log "${RED}Database connection: FAILED${NC}"
        send_telegram "❌ Database connection failed"
        return 1
    fi
}

# Check Redis connection
check_redis() {
    if docker exec steman_redis redis-cli ping > /dev/null 2>&1; then
        log "${GREEN}Redis connection: OK${NC}"
        return 0
    else
        log "${RED}Redis connection: FAILED${NC}"
        send_telegram "❌ Redis connection failed"
        return 1
    fi
}

# Main health check function
health_check() {
    log "Running health check..."
    
    local failures=0
    
    check_http_status || ((failures++))
    check_response_time || ((failures++))
    check_ssl || ((failures++))
    check_disk_space || ((failures++))
    check_docker_containers || ((failures++))
    check_database || ((failures++))
    check_redis || ((failures++))
    
    if [ "$failures" -eq 0 ]; then
        log "${GREEN}All health checks passed${NC}"
        return 0
    else
        log "${RED}$failures health check(s) failed${NC}"
        send_telegram "❌ $failures health check(s) failed"
        return 1
    fi
}

# Run health check
health_check
