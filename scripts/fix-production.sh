#!/bin/bash

echo "======================================"
echo "🚀 STEMAN PRODUCTION FIX"
echo "======================================"

set -e

echo "📦 Stop nginx"
docker stop steman_nginx || true
docker rm steman_nginx || true

echo "📦 Validate nginx config"
docker run --rm \
  -v $(pwd)/docker/nginx/nginx.conf:/etc/nginx/nginx.conf:ro \
  -v $(pwd)/docker/nginx/conf.d:/etc/nginx/conf.d:ro \
  macbre/nginx-brotli nginx -t || exit 1

echo "📦 Start stack"
docker compose up -d

echo "📦 Wait services"
sleep 10

echo "📦 Test HTTP"
curl -I http://localhost || echo "❌ HTTP not ready"

echo "======================================"
echo "✅ FIX DONE"
echo "======================================"
