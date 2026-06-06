#!/bin/bash

# SSL Certificate Automation Script
# This script automates SSL certificate management to prevent 521/526/500 errors

set -e

DOMAINS="alumni-steman.my.id alumni-steman.my.id-0001"
EMAIL="admin@alumni-steman.my.id"
CERT_DIR="/etc/letsencrypt/live"
BACKUP_DIR="/var/www/ssl-backups"
LOG_FILE="/var/log/ssl-automation.log"
WEBHOOK_URL=""  # Add your webhook URL for notifications

# Function to log messages
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Function to send notification
send_notification() {
    local message="$1"
    if [ -n "$WEBHOOK_URL" ]; then
        curl -X POST "$WEBHOOK_URL" -H "Content-Type: application/json" -d "{\"text\":\"$message\"}" > /dev/null 2>&1
    fi
}

# Function to check certificate validity
check_certificate() {
    local domain="$1"
    local cert_path="$CERT_DIR/$domain/fullchain.pem"
    
    # Check if certificate exists in certbot container
    if ! docker exec steman_certbot test -f "$cert_path"; then
        log "Certificate not found for $domain in certbot container"
        return 1
    fi
    
    # Get expiry date from certificate
    local expiry_date=$(docker exec steman_certbot openssl x509 -enddate -noout -in "$cert_path" | cut -d= -f2)
    local expiry_epoch=$(date -d "$expiry_date" +%s 2>/dev/null || echo "0")
    local current_epoch=$(date +%s)
    local days_until_expiry=$(( ($expiry_epoch - $current_epoch) / 86400 ))
    
    log "Certificate for $domain expires in $days_until_expiry days"
    
    if [ $days_until_expiry -lt 30 ]; then
        log "Certificate for $domain will expire soon ($days_until_expiry days)"
        return 0
    fi
    
    return 1
}

# Function to backup current certificates
backup_certificates() {
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local backup_path="$BACKUP_DIR/$timestamp"
    
    mkdir -p "$backup_path"
    
    for domain in $DOMAINS; do
        local cert_dir="$CERT_DIR/$domain"
        if docker exec steman_certbot test -d "$cert_dir"; then
            docker exec steman_certbot tar -c -C /etc/letsencrypt/live "$domain" | tar -x -C "$backup_path"
            log "Backed up certificate for $domain"
        fi
    done
    
    # Keep only last 7 backups
    ls -t "$BACKUP_DIR" | tail -n +8 | xargs -I {} rm -rf "$BACKUP_DIR/{}"
}

# Function to renew certificate
renew_certificate() {
    local domain="$1"
    
    log "Attempting to renew certificate for $domain"
    
    # Renew certificate using certbot
    docker exec steman_certbot certbot renew --cert-name "$domain" --non-interactive --agree-tos --email "$EMAIL" || {
        log "Failed to renew certificate for $domain"
        send_notification "SSL Certificate renewal FAILED for $domain"
        return 1
    }
    
    # Reload nginx
    docker exec steman_nginx nginx -s reload || {
        log "Failed to reload nginx"
        send_notification "SSL Certificate renewed but nginx reload FAILED"
        return 1
    }
    
    log "Successfully renewed certificate for $domain and reloaded nginx"
    send_notification "SSL Certificate renewed successfully for $domain"
    
    return 0
}

# Function to validate certificate
validate_certificate() {
    local domain="$1"
    local cert_path="$CERT_DIR/$domain/fullchain.pem"
    local key_path="$CERT_DIR/$domain/privkey.pem"
    
    if ! docker exec steman_certbot test -f "$cert_path" || ! docker exec steman_certbot test -f "$key_path"; then
        log "Certificate or key file missing for $domain"
        return 1
    fi
    
    # Check if certificate matches key (skip for ECDSA keys)
    local key_type=$(docker exec steman_certbot openssl rsa -in "$key_path" -noout -text 2>/dev/null | grep "Private-Key:" || echo "ECDSA")
    if [[ "$key_type" != *"ECDSA"* ]]; then
        local cert_modulus=$(docker exec steman_certbot openssl x509 -noout -modulus -in "$cert_path" | openssl md5)
        local key_modulus=$(docker exec steman_certbot openssl rsa -noout -modulus -in "$key_path" | openssl md5)
        
        if [ "$cert_modulus" != "$key_modulus" ]; then
            log "Certificate and key do not match for $domain"
            return 1
        fi
    fi
    
    # Check certificate validity period
    local not_before=$(docker exec steman_certbot openssl x509 -noout -startdate -in "$cert_path" | cut -d= -f2)
    local not_after=$(docker exec steman_certbot openssl x509 -noout -enddate -in "$cert_path" | cut -d= -f2)
    
    log "Certificate for $domain valid from $not_before to $not_after"
    
    return 0
}

# Main execution
main() {
    log "Starting SSL certificate automation"
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR"
    
    # Check each domain
    for domain in $DOMAINS; do
        if check_certificate "$domain"; then
            log "Certificate needs renewal for $domain"
            backup_certificates
            renew_certificate "$domain"
            validate_certificate "$domain"
        else
            validate_certificate "$domain" || {
                log "Certificate validation failed for $domain, attempting renewal"
                backup_certificates
                renew_certificate "$domain"
            }
        fi
    done
    
    log "SSL certificate automation completed"
}

# Run main function
main
