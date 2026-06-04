#!/bin/bash

echo "======================================"
echo "🔐 AUTO SSL RENEW"
echo "======================================"

DOMAIN="alumni-steman.my.id"

docker stop steman_nginx || true

certbot certonly --standalone \
  -d $DOMAIN \
  -d www.$DOMAIN \
  -d admin.$DOMAIN \
  -d api.$DOMAIN \
  --non-interactive \
  --agree-tos \
  --email alumnisteman@gmail.com

docker start steman_nginx

echo "======================================"
echo "✅ SSL DONE"
echo "======================================"
