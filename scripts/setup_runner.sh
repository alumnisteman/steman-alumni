#!/bin/bash
# ==========================================================
# Script Instalasi GitHub Actions Runner - Server STEMAN
# Jalankan script ini di server 192.168.1.5 via SSH
# ==========================================================

# ========================
# !! ISI NIL DULU Bapak !!
# ========================
# Ambil token dari: GitHub → web_forsa → Settings → Actions → Runners → New runner
GITHUB_TOKEN="PASTE_TOKEN_BAPAK_DISINI"
REPO_URL="https://github.com/alumnisteman/steman-alumni"
RUNNER_NAME="steman-server"
RUNNER_DIR="/opt/actions-runner"

echo "=============================================="
echo " Setup GitHub Runner - Portal Alumni STEMAN"
echo "=============================================="

# 1. Buat folder runner
echo "[1/5] Membuat folder runner di $RUNNER_DIR..."
sudo mkdir -p $RUNNER_DIR
sudo chown $USER:$USER $RUNNER_DIR
cd $RUNNER_DIR

# 2. Download runner terbaru
echo "[2/5] Mendownload GitHub Actions Runner..."
curl -o actions-runner-linux-x64-2.333.0.tar.gz -L \
  https://github.com/actions/runner/releases/download/v2.333.0/actions-runner-linux-x64-2.333.0.tar.gz

# 3. Ekstrak
echo "[3/5] Mengekstrak..."
tar xzf ./actions-runner-linux-x64-2.333.0.tar.gz

# 4. Konfigurasi Runner
echo "[4/5] Mendaftarkan runner ke GitHub..."
./config.sh \
  --url $REPO_URL \
  --token $GITHUB_TOKEN \
  --name $RUNNER_NAME \
  --labels steman-server \
  --unattended \
  --replace

# 5. Install sebagai Service (agar otomatis jalan saat server restart)
echo "[5/5] Menginstall sebagai Service (Auto-Start)..."
sudo ./svc.sh install
sudo ./svc.sh start

echo ""
echo "=============================================="
echo " SELESAI! GitHub Runner sudah aktif."
echo " Status: $(sudo ./svc.sh status)"
echo "=============================================="
