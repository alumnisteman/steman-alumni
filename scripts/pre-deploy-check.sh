#!/bin/bash
# =============================================================
# pre-deploy-check.sh - Cek kode sebelum deploy ke produksi
# Jalankan: bash scripts/pre-deploy-check.sh
# =============================================================
APP_DIR="/var/www/steman-alumni"
ERRORS=0

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'; BOLD='\033[1m'
log_ok()   { echo -e "  ${GREEN}[OK]${NC} $1"; }
log_err()  { echo -e "  ${RED}[ERROR]${NC} $1"; ERRORS=$((ERRORS+1)); }
log_warn() { echo -e "  ${YELLOW}[WARN]${NC} $1"; }

echo -e "\n${BOLD}========================================${NC}"
echo -e "${BOLD} PRE-DEPLOY CHECK - Alumni STEMAN${NC}"
echo -e "${BOLD}========================================${NC}\n"

cd "$APP_DIR"

# ----- 1. PHP Syntax Check -----
echo -e "${BOLD}[1/6] PHP Syntax Check...${NC}"
PHP_ERRORS=$(find app routes config bootstrap -name "*.php" 2>/dev/null | xargs -P4 -I{} php -l {} 2>&1 | grep -v "No syntax errors" || true)
if [ -n "$PHP_ERRORS" ]; then
  log_err "PHP syntax errors ditemukan:"
  echo "$PHP_ERRORS" | head -20
else
  log_ok "Semua file PHP syntax valid"
fi

# ----- 2. Cek Unicode Smart Quotes di PHP -----
echo -e "\n${BOLD}[2/6] Cek Unicode Smart Quotes di PHP...${NC}"
SMART_QUOTES=$(grep -rlP "\xe2\x80\x9c|\xe2\x80\x9d|\xe2\x80\x98|\xe2\x80\x99" app/Http/Controllers app/Models routes 2>/dev/null || true)
if [ -n "$SMART_QUOTES" ]; then
  log_err "Smart quotes/curly quotes ditemukan (penyebab syntax error!):"
  echo "$SMART_QUOTES" | head -10
else
  log_ok "Tidak ada smart quotes bermasalah"
fi

# ----- 3. Laravel Route Check (di container) -----
echo -e "\n${BOLD}[3/6] Laravel Route Check...${NC}"
ROUTE_OUT=$(docker exec steman_app php artisan route:list 2>&1)
ROUTE_EXIT=$?
ROUTE_ERR=$(echo "$ROUTE_OUT" | grep -E "^(PHP |  \[|ErrorException|ParseError|FatalError|In .+\.php line)" || true)
if [ $ROUTE_EXIT -ne 0 ] || [ -n "$ROUTE_ERR" ]; then
  log_err "Route registration gagal:"
  echo "$ROUTE_ERR" | head -5
else
  ROUTE_COUNT=$(echo "$ROUTE_OUT" | grep -cE "GET|POST|PUT|DELETE|PATCH" || echo "0")
  log_ok "Routes terdaftar: $ROUTE_COUNT routes"
fi

# ----- 4. Blade Template Cache Check -----
echo -e "\n${BOLD}[4/6] Blade Template Route Integrity...${NC}"
VIEW_OUT=$(docker exec steman_app php artisan view:cache 2>&1)
VIEW_ERR=$(echo "$VIEW_OUT" | grep -iE "ErrorException|RouteNotFoundException|ParseError|syntax error" || true)
if [ -n "$VIEW_ERR" ]; then
  log_err "Blade/view errors: $(echo "$VIEW_ERR" | head -3)"
else
  log_ok "Semua blade template OK"
fi

# ----- 5. Cek Critical Endpoints -----
echo -e "\n${BOLD}[5/6] Cek HTTP Endpoints...${NC}"
check_endpoint() {
  local path="$1" expected="$2"
  local actual
  actual=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "https://admin.alumni-steman.my.id${path}" 2>/dev/null || echo "000")
  if [ "$actual" = "$expected" ]; then
    log_ok "${path} -> HTTP $actual"
  else
    log_err "${path} -> HTTP $actual (expected $expected)"
  fi
}
check_endpoint "/health" "200"
check_endpoint "/login"  "200"
check_endpoint "/"       "302"

# ----- 6. Docker Container Health -----
echo -e "\n${BOLD}[6/6] Docker Container Health...${NC}"
UNHEALTHY=$(docker ps --format "{{.Names}}|{{.Status}}" 2>/dev/null | grep "steman_" | grep -v "healthy\|Up" || true)
if [ -n "$UNHEALTHY" ]; then
  log_err "Container tidak healthy: $UNHEALTHY"
else
  log_ok "Semua container sehat"
fi

# ----- Summary -----
echo -e "\n${BOLD}========================================${NC}"
if [ "$ERRORS" -gt 0 ]; then
  echo -e "${RED}${BOLD}GAGAL: $ERRORS error ditemukan${NC}"
  echo -e "${RED}Deploy DIBATALKAN. Perbaiki error di atas dulu.${NC}"
  exit 1
else
  echo -e "${GREEN}${BOLD}SEMUA CHECKS LULUS - Aman untuk deploy!${NC}"
  exit 0
fi
