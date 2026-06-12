#!/bin/bash
# Git pre-commit hook: cek PHP syntax & smart quotes sebelum commit
RED='\033[0;31m'; GREEN='\033[0;32m'; NC='\033[0m'
ERRORS=0

STAGED_PHP=$(git diff --cached --name-only --diff-filter=ACM | grep "\.php$" || true)
[ -z "$STAGED_PHP" ] && echo -e "${GREEN}No PHP files staged.${NC}" && exit 0

echo "Checking PHP syntax..."
for file in $STAGED_PHP; do
  [ ! -f "$file" ] && continue
  php -l "$file" > /dev/null 2>&1 || { echo -e "${RED}[SYNTAX ERROR] $file${NC}"; php -l "$file" 2>&1; ERRORS=$((ERRORS+1)); }
  grep -Pn "\xe2\x80\x9c|\xe2\x80\x9d|\xe2\x80\x98|\xe2\x80\x99" "$file" 2>/dev/null && { echo -e "${RED}[SMART QUOTES] $file${NC}"; ERRORS=$((ERRORS+1)); }
done

[ "$ERRORS" -gt 0 ] && echo -e "\n${RED}Commit DIBATALKAN: $ERRORS error.${NC}" && exit 1
echo -e "${GREEN}Semua PHP valid.${NC}" && exit 0
