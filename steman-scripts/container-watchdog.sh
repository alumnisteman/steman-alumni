#!/bin/bash
# ============================================================
# Container Memory Watchdog — Steman Alumni Production
# Jalankan sebagai cron job di HOST server (bukan di dalam container)
# Cron: */5 * * * * /bin/bash /var/www/steman-alumni/steman-scripts/container-watchdog.sh >> /var/log/steman-watchdog.log 2>&1
# ============================================================

COOLDOWN_DIR="/tmp/steman_watchdog_cooldown"
COOLDOWN_SECONDS=600   # 10 menit — cegah restart loop
STATUS_FILE="/tmp/steman_watchdog_status.json"
LOG_PREFIX="[WATCHDOG $(date '+%Y-%m-%d %H:%M:%S')]"

mkdir -p "$COOLDOWN_DIR"

# ── Load Telegram config dari .env ──────────────────────────
ENV_FILE="/var/www/steman-alumni/.env"
TELEGRAM_TOKEN=""
TELEGRAM_CHAT_ID=""

if [ -f "$ENV_FILE" ]; then
    TELEGRAM_TOKEN=$(grep -E '^TELEGRAM_BOT_TOKEN=' "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
    TELEGRAM_CHAT_ID=$(grep -E '^TELEGRAM_CHAT_ID=' "$ENV_FILE" | cut -d'=' -f2 | tr -d '"' | tr -d "'")
fi

# ── Fungsi kirim notifikasi Telegram ────────────────────────
notify_telegram() {
    local msg="$1"
    if [ -z "$TELEGRAM_TOKEN" ] || [ -z "$TELEGRAM_CHAT_ID" ]; then
        return 0
    fi
    curl -s --max-time 5 \
        "https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage" \
        -d "chat_id=${TELEGRAM_CHAT_ID}" \
        -d "parse_mode=Markdown" \
        --data-urlencode "text=${msg}" > /dev/null 2>&1 || true
}

# ── Definisi batas memori per container (dalam MB) ───────────
# AMAN: beri toleransi ~87% dari limit docker-compose
# TIDAK akan restart: steman_db (MariaDB) dan steman_redis
declare -A MEMORY_LIMITS
MEMORY_LIMITS["steman_app"]=445         # limit 512M → restart jika > 445M
MEMORY_LIMITS["steman_queue"]=220       # limit 256M → restart jika > 220M
MEMORY_LIMITS["steman_reverb"]=220      # limit 256M → restart jika > 220M
MEMORY_LIMITS["steman_grafana"]=220     # limit 256M → restart jika > 220M
MEMORY_LIMITS["steman_meilisearch"]=165 # limit 192M → restart jika > 165M
MEMORY_LIMITS["steman_prometheus"]=110  # limit 128M → restart jika > 110M
MEMORY_LIMITS["steman_nginx"]=110       # limit 128M → restart jika > 110M

# ── Fungsi cek apakah container dalam cooldown ──────────────
is_in_cooldown() {
    local container="$1"
    local cooldown_file="${COOLDOWN_DIR}/${container}"
    if [ -f "$cooldown_file" ]; then
        local last_restart
        last_restart=$(cat "$cooldown_file")
        local now
        now=$(date +%s)
        local elapsed=$(( now - last_restart ))
        if [ "$elapsed" -lt "$COOLDOWN_SECONDS" ]; then
            local remaining=$(( COOLDOWN_SECONDS - elapsed ))
            echo "$LOG_PREFIX  ⏳ ${container} masih dalam cooldown (${remaining}s tersisa). Skip."
            return 0  # true = masih cooldown
        fi
    fi
    return 1  # false = tidak dalam cooldown
}

# ── Fungsi set cooldown ──────────────────────────────────────
set_cooldown() {
    local container="$1"
    date +%s > "${COOLDOWN_DIR}/${container}"
}

# ── Fungsi ambil penggunaan memori container (dalam MB) ─────
get_memory_mb() {
    local container="$1"
    # docker stats menghasilkan format "123.4MiB" atau "1.2GiB"
    local mem_raw
    mem_raw=$(docker stats --no-stream --format "{{.MemUsage}}" "$container" 2>/dev/null | awk '{print $1}')

    if [ -z "$mem_raw" ]; then
        echo "0"
        return
    fi

    # Konversi ke MB
    if echo "$mem_raw" | grep -qi "GiB"; then
        echo "$mem_raw" | sed 's/GiB//i' | awk '{printf "%.0f", $1 * 1024}'
    elif echo "$mem_raw" | grep -qi "MiB"; then
        echo "$mem_raw" | sed 's/MiB//i' | awk '{printf "%.0f", $1}'
    elif echo "$mem_raw" | grep -qi "kib"; then
        echo "$mem_raw" | sed 's/kib//i' | awk '{printf "%.0f", $1 / 1024}'
    else
        echo "0"
    fi
}

# ── Fungsi restart container dengan aman ────────────────────
safe_restart() {
    local container="$1"
    local mem_used="$2"
    local mem_limit="$3"

    echo "$LOG_PREFIX 🔄 Merestart ${container} (${mem_used}MB / limit ${mem_limit}MB)"

    # Graceful stop dulu (15 detik timeout)
    docker stop --time=15 "$container" > /dev/null 2>&1 || true
    sleep 2
    docker start "$container" > /dev/null 2>&1

    local exit_code=$?
    if [ $exit_code -eq 0 ]; then
        set_cooldown "$container"
        echo "$LOG_PREFIX ✅ ${container} berhasil direstart"
        notify_telegram "🔄 *Container Auto-Restart*
Container \`${container}\` direstart otomatis oleh watchdog.
Memori: ${mem_used}MB (limit: ${mem_limit}MB)
Server: Steman Alumni Production
🕒 $(date '+%d %b %Y %H:%M:%S')"
        return 0
    else
        echo "$LOG_PREFIX ❌ Gagal restart ${container}!"
        notify_telegram "🚨 *Gagal Restart Container*
Watchdog gagal merestart \`${container}\`.
Memori: ${mem_used}MB — perlu perhatian manual!
🕒 $(date '+%d %b %Y %H:%M:%S')"
        return 1
    fi
}

# ── Main: cek semua container ────────────────────────────────
echo "$LOG_PREFIX === Mulai pengecekan memori container ==="

ALERT_MSGS=()
RESTARTED=()

for container in "${!MEMORY_LIMITS[@]}"; do
    limit="${MEMORY_LIMITS[$container]}"

    # Cek apakah container sedang running
    status=$(docker inspect --format='{{.State.Status}}' "$container" 2>/dev/null)
    if [ "$status" != "running" ]; then
        echo "$LOG_PREFIX  ⚠️  ${container} tidak running (status: ${status:-tidak ditemukan}). Skip."
        continue
    fi

    # Ambil penggunaan memori
    mem_used=$(get_memory_mb "$container")

    echo "$LOG_PREFIX  📊 ${container}: ${mem_used}MB / ${limit}MB"

    # Cek apakah melebihi batas
    if [ "$mem_used" -gt "$limit" ] 2>/dev/null; then
        echo "$LOG_PREFIX  🚨 ${container} melebihi batas memori! (${mem_used}MB > ${limit}MB)"

        if is_in_cooldown "$container"; then
            continue
        fi

        safe_restart "$container" "$mem_used" "$limit"
        RESTARTED+=("$container:${mem_used}MB")
    fi
done

# ── Tulis status ke file (dibaca oleh PHP SystemGuard) ───────
TOTAL_RAM_MB=$(free -m | awk 'NR==2{print $2}')
USED_RAM_MB=$(free -m | awk 'NR==2{print $3}')
RAM_PERCENT=$(( USED_RAM_MB * 100 / TOTAL_RAM_MB ))

cat > "$STATUS_FILE" <<EOF
{
    "checked_at": $(date +%s),
    "ram_total_mb": ${TOTAL_RAM_MB},
    "ram_used_mb": ${USED_RAM_MB},
    "ram_percent": ${RAM_PERCENT},
    "restarted_containers": $(printf '%s\n' "${RESTARTED[@]}" | python3 -c "import sys,json; print(json.dumps([l.strip() for l in sys.stdin if l.strip()]))" 2>/dev/null || echo "[]")
}
EOF

# ── Kirim alert jika RAM server sendiri hampir penuh (> 90%) ─
if [ "$RAM_PERCENT" -gt 90 ]; then
    echo "$LOG_PREFIX 🚨 RAM SERVER KRITIS: ${USED_RAM_MB}MB / ${TOTAL_RAM_MB}MB (${RAM_PERCENT}%)"
    notify_telegram "🚨 *RAM Server Kritis!*
Penggunaan RAM: *${USED_RAM_MB}MB / ${TOTAL_RAM_MB}MB (${RAM_PERCENT}%)*
Ambang kritis: 90%
Pertimbangkan upgrade VPS atau matikan service non-kritis.
🕒 $(date '+%d %b %Y %H:%M:%S')"
elif [ "$RAM_PERCENT" -gt 80 ]; then
    echo "$LOG_PREFIX ⚠️ RAM server tinggi: ${USED_RAM_MB}MB / ${TOTAL_RAM_MB}MB (${RAM_PERCENT}%)"
fi

echo "$LOG_PREFIX === Selesai. RAM: ${USED_RAM_MB}/${TOTAL_RAM_MB}MB (${RAM_PERCENT}%) ==="
