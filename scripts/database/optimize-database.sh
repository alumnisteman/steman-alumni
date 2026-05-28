#!/bin/bash

# Database Optimization Script for Steman Alumni
# Optimizes database performance and adds missing indexes

set -e

# Configuration
DB_HOST="steman_db"
DB_USER="root"
DB_PASSWORD="${DB_PASSWORD:-}"
DB_NAME="steman_alumni"
LOG_FILE="/var/www/steman-alumni/storage/logs/database-optimization.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Logging
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Execute SQL command
exec_sql() {
    docker exec -i "$DB_HOST" mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "$1"
}

# Analyze tables
analyze_tables() {
    log "Analyzing tables..."
    exec_sql "ANALYZE TABLE users, feeds, comments, likes, stories, businesses, jobs, podcasts, news, galleries, polls, poll_votes, poll_options;"
    log "${GREEN}Tables analyzed${NC}"
}

# Optimize tables
optimize_tables() {
    log "Optimizing tables..."
    exec_sql "OPTIMIZE TABLE users, feeds, comments, likes, stories, businesses, jobs, podcasts, news, galleries, polls, poll_votes, poll_options;"
    log "${GREEN}Tables optimized${NC}"
}

# Check for missing indexes
check_indexes() {
    log "Checking for missing indexes..."
    
    # Check for foreign key indexes
    exec_sql "
        SELECT 
            TABLE_NAME, 
            COLUMN_NAME, 
            CONSTRAINT_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = '$DB_NAME'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND COLUMN_NAME NOT IN (
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.STATISTICS 
                WHERE TABLE_SCHEMA = '$DB_NAME'
                AND TABLE_NAME = INFORMATION_SCHEMA.KEY_COLUMN_USAGE.TABLE_NAME
            );
    " | tee -a "$LOG_FILE"
}

# Add missing indexes
add_indexes() {
    log "Adding missing indexes..."
    
    # Common indexes that should exist
    exec_sql "
        -- Add index if not exists
        ALTER TABLE feeds ADD INDEX IF NOT EXISTS idx_feeds_user_id (user_id);
        ALTER TABLE feeds ADD INDEX IF NOT EXISTS idx_feeds_created_at (created_at);
        ALTER TABLE comments ADD INDEX IF NOT EXISTS idx_comments_feed_id (feed_id);
        ALTER TABLE comments ADD INDEX IF NOT EXISTS idx_comments_user_id (user_id);
        ALTER TABLE likes ADD INDEX IF NOT EXISTS idx_likes_feed_id (feed_id);
        ALTER TABLE likes ADD INDEX IF NOT EXISTS idx_likes_user_id (user_id);
        ALTER TABLE stories ADD INDEX IF NOT EXISTS idx_stories_user_id (user_id);
        ALTER TABLE stories ADD INDEX IF NOT EXISTS idx_stories_expires_at (expires_at);
        ALTER TABLE businesses ADD INDEX IF NOT EXISTS idx_businesses_user_id (user_id);
        ALTER TABLE jobs ADD INDEX IF NOT EXISTS idx_jobs_user_id (user_id);
        ALTER TABLE jobs ADD INDEX IF NOT EXISTS idx_jobs_created_at (created_at);
        ALTER TABLE poll_votes ADD INDEX IF NOT EXISTS idx_poll_votes_user_id (user_id);
        ALTER TABLE poll_votes ADD INDEX IF NOT EXISTS idx_poll_votes_poll_id (poll_id);
    "
    
    log "${GREEN}Indexes added${NC}"
}

# Check slow queries
check_slow_queries() {
    log "Checking for slow queries..."
    
    exec_sql "
        SELECT 
            DIGEST_TEXT AS query,
            COUNT_STAR AS exec_count,
            AVG_TIMER_WAIT/1000000000000 AS avg_time_sec,
            SUM_TIMER_WAIT/1000000000000 AS total_time_sec
        FROM 
            performance_schema.events_statements_summary_by_digest
        WHERE 
            SCHEMA_NAME = '$DB_NAME'
            AND AVG_TIMER_WAIT/1000000000000 > 0.1
        ORDER BY 
            AVG_TIMER_WAIT DESC
        LIMIT 10;
    " | tee -a "$LOG_FILE"
}

# Optimize configuration
optimize_config() {
    log "Optimizing database configuration..."
    
    # Update MySQL configuration for better performance
    exec_sql "
        SET GLOBAL innodb_buffer_pool_size = 256M;
        SET GLOBAL innodb_log_file_size = 64M;
        SET GLOBAL query_cache_size = 32M;
        SET GLOBAL query_cache_type = 1;
        SET GLOBAL max_connections = 150;
    "
    
    log "${GREEN}Configuration optimized${NC}"
}

# Main optimization function
optimize() {
    log "Starting database optimization..."
    
    analyze_tables
    optimize_tables
    check_indexes
    add_indexes
    check_slow_queries
    optimize_config
    
    log "${GREEN}Database optimization completed${NC}"
}

# Run optimization
optimize
