#!/bin/bash
# Lightweight URL monitor — alert only, no auto-restart.
# Usage: ./scripts/monitor_site.sh

set -euo pipefail

PROJECT_DIR="${PROJECT_DIR:-/var/www/steman-alumni}"
LOG_FILE="${LOG_FILE:-/var/log/monitor-site.log}"

# Load Telegram credentials from .env when available
if [[ -f "$PROJECT_DIR/.env" ]]; then
    set -a
    # shellcheck disable=SC1091
    source <(grep -E '^(TELEGRAM_BOT_TOKEN|TELEGRAM_CHAT_ID)=' "$PROJECT_DIR/.env" | sed 's/\r$//')
    set +a
fi

log() { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"; }

notify() {
    local msg="$1"
    log "ALERT: $msg"
    if [[ -n "${TELEGRAM_BOT_TOKEN:-}" && -n "${TELEGRAM_CHAT_ID:-}" ]]; then
        curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
            -d "chat_id=${TELEGRAM_CHAT_ID}" \
            --data-urlencode "text=${msg}" >/dev/null 2>&1 || true
    fi
}

check() {
    local url="$1"
    local expect="${2:-200}"
    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null || echo "000")
    if [[ "$code" == "$expect" ]] || [[ "$expect" == "any" && "$code" =~ ^(200|301|302)$ ]]; then
        log "OK $url → HTTP $code"
        return 0
    fi
    notify "⚠️ Steman Alumni: $url returned HTTP $code (expected $expect)"
    return 1
}

log "Site monitor started"
ISSUES=0
check "https://alumni-steman.my.id/" "any" || ISSUES=$((ISSUES + 1))
check "https://admin.alumni-steman.my.id/login" "any" || ISSUES=$((ISSUES + 1))
check "https://alumni-steman.my.id/health" "200" || ISSUES=$((ISSUES + 1))

if [[ $ISSUES -eq 0 ]]; then
    log "All checks passed"
else
    log "$ISSUES check(s) failed"
fi

exit 0
