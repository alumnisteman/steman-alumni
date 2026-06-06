#!/bin/bash
# Post-deploy verification — run after code/asset updates on the server.
# Usage: ./scripts/post_deploy_check.sh [project_dir]

set -euo pipefail

PROJECT_DIR="${1:-/var/www/steman-alumni}"
APP_CONTAINER="${APP_CONTAINER:-steman_app}"
FAIL=0

log() { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"; }
fail() { log "FAIL: $*"; FAIL=1; }
pass() { log "OK: $*"; }

log "Post-deploy check starting (project: $PROJECT_DIR)"

# 1. Vite manifest + built assets
MANIFEST="$PROJECT_DIR/public/build/manifest.json"
if [[ ! -f "$MANIFEST" ]]; then
    fail "Missing public/build/manifest.json — run: npm ci && npm run build"
else
    if grep -q 'resources/sass/app.scss' "$MANIFEST" && grep -q 'assets/app-' "$MANIFEST"; then
        pass "Vite manifest looks valid"
    else
        fail "Vite manifest appears stale or dev-only: $MANIFEST"
    fi
fi

# 2. Storage symlink
if docker exec "$APP_CONTAINER" test -L /var/www/public/storage 2>/dev/null || \
   docker exec "$APP_CONTAINER" test -d /var/www/public/storage 2>/dev/null; then
    pass "public/storage is accessible in container"
else
    fail "public/storage missing — run: docker exec $APP_CONTAINER php artisan storage:link"
fi

# 3. PHP extensions for uploads
if docker exec "$APP_CONTAINER" php -m 2>/dev/null | grep -qi gd; then
    pass "PHP GD extension available (image uploads)"
else
    fail "PHP GD extension missing — thumbnail optimization will fail"
fi

# 4. Laravel optimize + migrate status
if docker exec "$APP_CONTAINER" php artisan migrate --force --no-interaction >/dev/null 2>&1; then
    pass "Migrations up to date"
else
    fail "Migration check failed"
fi

docker exec "$APP_CONTAINER" php artisan config:clear >/dev/null 2>&1 || true
docker exec "$APP_CONTAINER" php artisan view:clear >/dev/null 2>&1 || true
pass "Laravel caches cleared"

# 5. HTTP smoke tests
check_url() {
    local url="$1"
    local label="$2"
    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null || echo "000")
    if [[ "$code" =~ ^(200|301|302)$ ]]; then
        pass "$label ($code)"
    else
        fail "$label returned HTTP $code"
    fi
}

check_url "https://alumni-steman.my.id/" "Homepage"
check_url "https://admin.alumni-steman.my.id/login" "Admin login"

# 6. Optional env warnings
if [[ -f "$PROJECT_DIR/.env" ]]; then
    if grep -q '^NEWS_API_KEY=' "$PROJECT_DIR/.env" && ! grep -q '^NEWS_API_KEY=$' "$PROJECT_DIR/.env"; then
        pass "NEWS_API_KEY is configured"
    else
        log "WARN: NEWS_API_KEY not set — Pulse will show News API DOWN (RSS feed still works)"
    fi
fi

if [[ $FAIL -eq 0 ]]; then
    log "Post-deploy check PASSED"
    exit 0
else
    log "Post-deploy check FAILED — review issues above"
    exit 1
fi
