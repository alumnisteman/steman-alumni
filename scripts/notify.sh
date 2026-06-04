#!/usr/bin/env bash
# Telegram notification helper
# Usage: notify.sh "Subject" "Message"

TOKEN="${TELEGRAM_BOT_TOKEN}"
CHAT_ID="${TELEGRAM_CHAT_ID}"
SUBJECT="$1"
MESSAGE="$2"

if [[ -z "$TOKEN" || -z "$CHAT_ID" ]]; then
  echo "Telegram credentials not set in environment" >&2
  exit 1
fi

curl -s -X POST "https://api.telegram.org/bot${TOKEN}/sendMessage" \
  -d chat_id="${CHAT_ID}" \
  -d parse_mode="Markdown" \
  -d text="*${SUBJECT}*\n${MESSAGE}"
