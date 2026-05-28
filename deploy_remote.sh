#!/usr/bin/env bash
# ------------------------------------------------------------
# Deploy steman‑alumni to remote server 103.175.219.57
# Uses plink (PuTTY) with SSH key for automated deployment
# ------------------------------------------------------------

REMOTE_HOST="103.175.219.57"
REMOTE_USER="root"
REMOTE_DIR="/var/www/steman-alumni"
REPO_URL="git@github.com:alumnisteman/steman-alumni.git"
KEY_PATH="${HOME}/.ssh/github-deploy"
PLINK="ssh"

# ----------------------------------------------------------------
# 1. Write the private key (ensure it exists on the machine running this script)
cat <<'EOF' > "${KEY_PATH}"
-----BEGIN OPENSSH PRIVATE KEY-----
... (key content omitted for brevity) ...
-----END OPENSSH PRIVATE KEY-----
EOF
chmod 600 "${KEY_PATH}"

# ----------------------------------------------------------------
# Helper to run a remote command via plink
run_remote() {
    "${PLINK}" -i "${KEY_PATH}" -o StrictHostKeyChecking=no "${REMOTE_USER}@${REMOTE_HOST}" "$1"
}

# ----------------------------------------------------------------
# 2. Ensure remote directory exists
run_remote "mkdir -p ${REMOTE_DIR}"

# ----------------------------------------------------------------
# 3. Clone or pull the repository
run_remote "cd ${REMOTE_DIR} && (git rev-parse --is-inside-work-tree && git pull || git clone ${REPO_URL} .)"

# ----------------------------------------------------------------
# 4. Install Composer dependencies (install Composer if missing)
run_remote "cd ${REMOTE_DIR} && if ! command -v composer > /dev/null 2>&1; then curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer; fi && composer install --no-dev --optimize-autoloader && composer dump-autoload"

# ----------------------------------------------------------------
# 5. Laravel cache & config optimisation
run_remote "cd ${REMOTE_DIR} && php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear && php artisan optimize"

# ----------------------------------------------------------------
# 6. Run database migrations safely (skip migrate:install if migrations table exists)
run_remote "cd ${REMOTE_DIR} && if php artisan migrate:status | grep -q 'No migrations were executed.'; then echo 'Migrations table already exists – skipping migrate:install.'; else php artisan migrate --force; fi"

# ----------------------------------------------------------------
# 7. Ensure the admin user has the proper role (idempotent)
run_remote "cd ${REMOTE_DIR} && php artisan tinker --execute "\App\Models\User::where('email','valingir@gmail.com')->update(['role'=>'admin']);""

# ----------------------------------------------------------------
# 8. (Optional) Restart Docker containers if using Docker‑Compose
# Uncomment the following line if your production stack runs via Docker‑Compose
# run_remote "cd ${REMOTE_DIR} && docker compose -f docker-compose.prod.yml up -d --build"

# ----------------------------------------------------------------
echo "✅ Deploy selesai. Buka https://alumni-steman.my.id/polls untuk verifikasi."
