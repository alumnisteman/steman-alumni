#!/bin/bash
# scripts/generate-ssl.sh - Generate self-signed SSL for Nginx (VPS)

SSL_DIR="./docker/nginx/ssl"

if [ ! -d "$SSL_DIR" ]; then
    mkdir -p "$SSL_DIR"
fi

if [ ! -f "$SSL_DIR/selfsigned.crt" ]; then
    echo "-> Generating self-signed SSL certificate..."
    # Using Docker to run OpenSSL to avoid local dependency issues
    docker run --rm -v "$(pwd)/$SSL_DIR:/export" alpine/openssl req -x509 -nodes -days 365 \
        -newkey rsa:2048 \
        -keyout /export/selfsigned.key \
        -out /export/selfsigned.crt \
        -subj "/C=ID/ST=Jawa Timur/L=Malang/O=STEMAN/OU=Alumni/CN=steman-alumni.com"
    
    sudo chmod 644 "$SSL_DIR/selfsigned.crt"
    sudo chmod 644 "$SSL_DIR/selfsigned.key"
    echo "-> Done! Certificates generated in $SSL_DIR"
else
    echo "-> SSL certificates already exist."
fi
