#!/bin/bash
# =============================================================================
#   STEMAN Alumni - Script Update Otomatis
#   Fungsi: Update Composer & Migrasi Database
# =============================================================================

set -e

# --- Warna Terminal ---
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

print_header() {
    echo -e "${CYAN}${BOLD}=============================================${NC}"
    echo -e "${CYAN}${BOLD}  $1${NC}"
    echo -e "${CYAN}${BOLD}=============================================${NC}"
}

print_step()  { echo -e "${GREEN}[✔] $1${NC}"; }
print_info()  { echo -e "${YELLOW}[→] $1${NC}"; }
print_error() { echo -e "${RED}[✘] ERROR: $1${NC}"; exit 1; }

# Pengecekan file artisan
if [ ! -f "artisan" ]; then
    print_error "Jalankan script dari dalam direktori aplikasi (yang ada file 'artisan')."
fi

print_header "Update Aplikasi STEMAN Alumni"

# 1. Update Composer
print_info "Menjalankan composer update..."
composer update --no-interaction --optimize-autoloader
print_step "Composer berhasil diperbarui."

# 2. Jalankan Migrasi
print_info "Menjalankan migrasi database..."
php artisan migrate --force
print_step "Migrasi database berhasil."

# 3. Clear Cache (Optimasi)
print_info "Membersihkan cache..."
php artisan optimize:clear
print_step "Cache berhasil dibersihkan."

print_header "✅ UPDATE SELESAI!"
