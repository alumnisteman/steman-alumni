#!/usr/bin/env bash
# ------------------------------------------------------------
# Deploy steman‑alumni to remote server 103.175.219.57
# Uses plink (PuTTY) with the provided SSH private key (github‑deploy)
# ------------------------------------------------------------

REMOTE_HOST="103.175.219.57"
REMOTE_USER="root"
REMOTE_DIR="/root/steman-alumni"
REPO_URL="git@github.com:alumnisteman/steman-alumni.git"
KEY_PATH="${HOME}/.ssh/github-deploy"
PLINK="plink"

# ------------------------------------------------------------
# 1. Write the private key to ${KEY_PATH}
cat <<'EOF' > "${KEY_PATH}"
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtzc2gtZW
QyNTUxOQAAACCdCzYdtKUrWHKKjRTJ04K9vpf9GGxBh+B/N6kW95gzzQAAAJjgI/Z64CP2
egAAAAtzc2gtZWQyNTUxOQAAACCdCzYdtKUrWHKKjRTJ04K9vpf9GGxBh+B/N6kW95gzzQ
AAAEBKN6oIhAMELQfG5SUQ6f3lKxg45FCSy1Bl1pgk4Gt01J0LNh20pStYcoqNFMnTgr2+
l/0YbEGH4H83qRb3mDPNAAAAEnN2bXMtcmVwbGl0LWRlcGxveQECAw==
-----END OPENSSH PRIVATE KEY-----
EOF
chmod 600 "${KEY_PATH}"

# ------------------------------------------------------------
# Helper to run a remote command via plink
run_remote() {
    "${PLINK}" -ssh -i "${KEY_PATH}" "${REMOTE_USER}@${REMOTE_HOST}" "$1"
}

# ------------------------------------------------------------
# 2. Create target directory on remote host
run_remote "mkdir -p ${REMOTE_DIR}"

# 3. Clone or pull the repository
run_remote "cd ${REMOTE_DIR} && (git rev-parse --is-inside-work-tree && git pull || git clone ${REPO_URL} .)"

# 4. Install Composer dependencies (install Composer if missing)
run_remote "cd ${REMOTE_DIR} && if ! command -v composer >/dev/null 2>&1; then curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer; fi && composer install --no-dev --optimize-autoloader"

# 5. Laravel cache & config optimisation
run_remote "cd ${REMOTE_DIR} && php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear && php artisan optimize"

# 6. Build and start Docker‑Compose production stack
run_remote "cd ${REMOTE_DIR} && docker compose -f docker-compose.prod.yml up -d --build"

# 7. Run database migrations
run_remote "cd ${REMOTE_DIR} && php artisan migrate --force"

# 8. Show running containers status
run_remote "docker ps --filter 'ancestor=steman-alumni' --format '{{.Names}} {{.Status}}'"

echo "✅ Deploy selesai. Buka https://alumni-steman.my.id/polls untuk verifikasi."
