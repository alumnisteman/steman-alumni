#!/bin/bash

# Health Check Script for Steman Alumni Containers
# Monitors container health and restarts if needed

set -e

LOG_FILE="/var/log/health-check.log"
PROJECT_DIR="${PROJECT_DIR:-/var/www/steman-alumni}"
WEBHOOK_URL=""  # Add your webhook URL for notifications
TELEGRAM_BOT_TOKEN=""
TELEGRAM_CHAT_ID=""

# Load Telegram credentials from project .env when present
if [[ -f "$PROJECT_DIR/.env" ]]; then
    set -a
    # shellcheck disable=SC1091
    source <(grep -E '^(TELEGRAM_BOT_TOKEN|TELEGRAM_CHAT_ID)=' "$PROJECT_DIR/.env" | sed 's/\r$//')
    set +a
fi

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Function to send notification
send_notification() {
    local message="$1"
    log "NOTIFICATION: $message"
    
    if [ -n "$WEBHOOK_URL" ]; then
        curl -X POST "$WEBHOOK_URL" -H "Content-Type: application/json" -d "{\"text\":\"$message\"}" > /dev/null 2>&1
    fi
    
    if [ -n "$TELEGRAM_BOT_TOKEN" ] && [ -n "$TELEGRAM_CHAT_ID" ]; then
        curl -s "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" -d "chat_id=$TELEGRAM_CHAT_ID" -d "text=$message" > /dev/null 2>&1
    fi
}

# Function to check container health
check_container() {
    local container="$1"
    local max_retries=3
    local retry_count=0
    
    while [ $retry_count -lt $max_retries ]; do
        if docker inspect "$container" > /dev/null 2>&1; then
            local status=$(docker inspect --format='{{.State.Health.Status}}' "$container" 2>/dev/null || echo "unknown")
            local running=$(docker inspect --format='{{.State.Running}}' "$container")
            
            if [ "$running" = "true" ]; then
                if [ "$status" = "healthy" ] || [ "$status" = "unknown" ]; then
                    log "Container $container is healthy"
                    return 0
                else
                    log "Container $container status: $status"
                    return 1
                fi
            else
                log "Container $container is not running"
                return 1
            fi
        else
            log "Container $container does not exist"
            return 1
        fi
        
        retry_count=$((retry_count + 1))
        sleep 5
    done
    
    return 1
}

# Function to restart container
restart_container() {
    local container="$1"
    log "Attempting to restart container $container"
    
    docker restart "$container" || {
        log "Failed to restart container $container"
        send_notification "CRITICAL: Failed to restart container $container"
        return 1
    }
    
    sleep 10
    
    if check_container "$container"; then
        log "Container $container restarted successfully"
        send_notification "INFO: Container $container restarted successfully"
        return 0
    else
        log "Container $container still unhealthy after restart"
        send_notification "CRITICAL: Container $container still unhealthy after restart"
        return 1
    fi
}

# Function to check website accessibility
check_website() {
    local url="$1"
    local expected_code="${2:-200}"
    
    local response=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$url" 2>/dev/null || echo "000")
    
    if [ "$response" = "$expected_code" ] || { [ "$expected_code" = "302" ] && [ "$response" = "301" ]; }; then
        log "Website $url is accessible (HTTP $response)"
        return 0
    else
        log "Website $url returned HTTP $response (expected $expected_code)"
        return 1
    fi
}

# Function to check SSL certificate
check_ssl() {
    local domain="$1"
    local port="${2:-443}"
    
    local expiry_date=$(echo | openssl s_client -servername "$domain" -connect "$domain:$port" 2>/dev/null | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)
    
    if [ -z "$expiry_date" ]; then
        log "Failed to get SSL certificate for $domain"
        return 1
    fi
    
    local expiry_epoch=$(date -d "$expiry_date" +%s 2>/dev/null || echo "0")
    local current_epoch=$(date +%s)
    local days_until_expiry=$(( ($expiry_epoch - $current_epoch) / 86400 ))
    
    log "SSL certificate for $domain expires in $days_until_expiry days"
    
    if [ $days_until_expiry -lt 7 ]; then
        log "WARNING: SSL certificate for $domain expires in less than 7 days"
        send_notification "WARNING: SSL certificate for $domain expires in $days_until_expiry days"
        return 1
    fi
    
    return 0
}

# Function to check disk space
check_disk_space() {
    local threshold=80
    local usage=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    
    if [ "$usage" -gt "$threshold" ]; then
        log "WARNING: Disk usage is at ${usage}% (threshold: ${threshold}%)"
        send_notification "WARNING: Disk usage is at ${usage}%"
        return 1
    fi
    
    log "Disk usage is at ${usage}%"
    return 0
}

# Function to check memory usage
check_memory() {
    local threshold=90
    local usage=$(free | awk 'NR==2 {printf "%.0f", $3/$2*100}')
    
    if [ "$usage" -gt "$threshold" ]; then
        log "WARNING: Memory usage is at ${usage}% (threshold: ${threshold}%)"
        send_notification "WARNING: Memory usage is at ${usage}%"
        return 1
    fi
    
    log "Memory usage is at ${usage}%"
    return 0
}

# Main health check function
main() {
    log "Starting health check"
    
    local critical_issues=0
    
    # Check containers
    local containers=("steman_app" "steman_nginx" "steman_db" "steman_redis" "steman_queue")
    
    for container in "${containers[@]}"; do
        if ! check_container "$container"; then
            log "CRITICAL: Container $container is unhealthy"
            critical_issues=$((critical_issues + 1))
            
            if restart_container "$container"; then
                critical_issues=$((critical_issues - 1))
            fi
        fi
    done
    
    # Check websites
    local websites=(
        "https://alumni-steman.my.id:200"
        "https://admin.alumni-steman.my.id:302"
    )
    
    for website in "${websites[@]}"; do
        local url=$(echo "$website" | cut -d: -f1)
        local expected_code=$(echo "$website" | cut -d: -f2)
        
        if ! check_website "$url" "$expected_code"; then
            log "CRITICAL: Website $url is not accessible"
            critical_issues=$((critical_issues + 1))
        fi
    done
    
    # Check SSL certificates
    local domains=("alumni-steman.my.id" "admin.alumni-steman.my.id")
    
    for domain in "${domains[@]}"; do
        if ! check_ssl "$domain"; then
            critical_issues=$((critical_issues + 1))
        fi
    done
    
    # Check system resources
    check_disk_space || critical_issues=$((critical_issues + 1))
    check_memory || critical_issues=$((critical_issues + 1))
    
    # Summary
    if [ $critical_issues -eq 0 ]; then
        log "Health check completed successfully - no critical issues"
    else
        log "Health check completed - $critical_issues critical issues found"
        send_notification "CRITICAL: Health check found $critical_issues critical issues"
    fi
}

# Run main function
main
