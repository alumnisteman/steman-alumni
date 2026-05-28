#!/bin/bash

# Auto-Deployment Script for Steman Alumni
# This script automates the deployment process with safety checks

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/steman-alumni"
BACKUP_DIR="$PROJECT_DIR/storage/backups"
LOG_FILE="$PROJECT_DIR/storage/logs/deployment.log"
TELEGRAM_BOT_TOKEN="${TELEGRAM_BOT_TOKEN:-}"
TELEGRAM_CHAT_ID="${TELEGRAM_CHAT_ID:-}"

# Logging function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Telegram notification function
send_telegram() {
    local message="$1"
    if [ -n "$TELEGRAM_BOT_TOKEN" ] && [ -n "$TELEGRAM_CHAT_ID" ]; then
        curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
            -d "chat_id=$TELEGRAM_CHAT_ID" \
            -d "text=$message" \
            -d "parse_mode=HTML" > /dev/null 2>&1
    fi
}

# Pre-deployment checks
pre_deploy_checks() {
    log "Running pre-deployment checks..."
    
    # Check if project directory exists
    if [ ! -d "$PROJECT_DIR" ]; then
        log "${RED}ERROR: Project directory not found${NC}"
        send_telegram "❌ Deployment FAILED: Project directory not found"
        exit 1
    fi
    
    # Check if Docker is running
    if ! docker ps > /dev/null 2>&1; then
        log "${RED}ERROR: Docker is not running${NC}"
        send_telegram "❌ Deployment FAILED: Docker is not running"
        exit 1
    fi
    
    # Check disk space
    DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
    if [ "$DISK_USAGE" -gt 80 ]; then
        log "${YELLOW}WARNING: Disk usage is at ${DISK_USAGE}%${NC}"
        send_telegram "⚠️ Deployment WARNING: Disk usage at ${DISK_USAGE}%"
    fi
    
    log "${GREEN}Pre-deployment checks passed${NC}"
}

# Backup function
backup() {
    log "Creating backup..."
    BACKUP_NAME="backup_$(date +%Y%m%d_%H%M%S)"
    BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"
    
    mkdir -p "$BACKUP_PATH"
    
    # Backup database
    docker exec steman_db mysqldump -u root -p"${DB_PASSWORD}" steman_alumni > "$BACKUP_PATH/database.sql"
    
    # Backup storage
    tar -czf "$BACKUP_PATH/storage.tar.gz" -C "$PROJECT_DIR" storage/app storage/framework
    
    # Backup .env
    cp "$PROJECT_DIR/.env" "$BACKUP_PATH/.env"
    
    log "${GREEN}Backup created at $BACKUP_PATH${NC}"
    echo "$BACKUP_NAME"
}

# Pull latest changes
pull_changes() {
    log "Pulling latest changes from GitHub..."
    cd "$PROJECT_DIR"
    
    # Stash local changes if any
    git stash push -m "Auto-stash before deployment $(date)" || true
    
    # Pull latest changes
    git pull origin main
    
    log "${GREEN}Changes pulled successfully${NC}"
}

# Install dependencies
install_dependencies() {
    log "Installing dependencies..."
    cd "$PROJECT_DIR"
    
    # Composer install
    docker exec steman-alumni-app-1 composer install --no-dev --optimize-autoloader --no-interaction
    
    log "${GREEN}Dependencies installed${NC}"
}

# Run migrations
run_migrations() {
    log "Running database migrations..."
    cd "$PROJECT_DIR"
    
    docker exec steman-alumni-app-1 php artisan migrate --force
    
    log "${GREEN}Migrations completed${NC}"
}

# Clear caches
clear_caches() {
    log "Clearing caches..."
    cd "$PROJECT_DIR"
    
    docker exec steman-alumni-app-1 php artisan cache:clear
    docker exec steman-alumni-app-1 php artisan config:clear
    docker exec steman-alumni-app-1 php artisan route:clear
    docker exec steman-alumni-app-1 php artisan view:clear
    
    log "${GREEN}Caches cleared${NC}"
}

# Restart services
restart_services() {
    log "Restarting services..."
    
    docker restart steman-alumni-app-1 steman-alumni-queue-1 steman_reverb steman_nginx
    
    # Wait for services to be healthy
    sleep 10
    
    log "${GREEN}Services restarted${NC}"
}

# Health check
health_check() {
    log "Running health check..."
    
    # Check if app is responding
    if curl -f -s https://alumni-steman.my.id > /dev/null; then
        log "${GREEN}Health check passed${NC}"
        return 0
    else
        log "${RED}ERROR: Health check failed${NC}"
        return 1
    fi
}

# Rollback function
rollback() {
    local backup_name="$1"
    log "${RED}Initiating rollback...${NC}"
    
    BACKUP_PATH="$BACKUP_DIR/$backup_name"
    
    if [ ! -d "$BACKUP_PATH" ]; then
        log "${RED}ERROR: Backup not found${NC}"
        exit 1
    fi
    
    # Restore database
    docker exec -i steman_db mysql -u root -p"${DB_PASSWORD}" steman_alumni < "$BACKUP_PATH/database.sql"
    
    # Restore storage
    tar -xzf "$BACKUP_PATH/storage.tar.gz" -C "$PROJECT_DIR"
    
    # Restore .env
    cp "$BACKUP_PATH/.env" "$PROJECT_DIR/.env"
    
    # Restart services
    restart_services
    
    log "${GREEN}Rollback completed${NC}"
    send_telegram "🔄 Rollback completed: $backup_name"
}

# Main deployment function
deploy() {
    log "Starting deployment..."
    send_telegram "🚀 Deployment started"
    
    # Pre-deployment checks
    pre_deploy_checks
    
    # Create backup
    BACKUP_NAME=$(backup)
    
    # Pull changes
    pull_changes
    
    # Install dependencies
    install_dependencies
    
    # Run migrations
    run_migrations
    
    # Clear caches
    clear_caches
    
    # Restart services
    restart_services
    
    # Health check
    if health_check; then
        log "${GREEN}Deployment successful!${NC}"
        send_telegram "✅ Deployment successful"
        
        # Cleanup old backups (keep last 5)
        cd "$BACKUP_DIR"
        ls -t | tail -n +6 | xargs -r rm -rf
        
        exit 0
    else
        log "${RED}Deployment failed, initiating rollback...${NC}"
        send_telegram "❌ Deployment failed, initiating rollback"
        rollback "$BACKUP_NAME"
        exit 1
    fi
}

# Run deployment
deploy
