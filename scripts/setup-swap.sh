#!/bin/sh
# setup-swap.sh - Emergency memory for low-RAM servers
# ====================================================
# This script adds a 2GB swap file to help with builds and high-traffic spikes.

# Check if swap already exists
if [ -n "$(swapon --show)" ]; then
    echo "Swap already exists. Skipping."
    exit 0
fi

echo "Setting up 2GB swap file..."

# Create a 2GB file
fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile

# Make it permanent
echo '/swapfile none swap sw 0 0' | tee -a /etc/fstab

# Optimize swap usage (swappiness 10 = use swap only when necessary)
sysctl vm.swappiness=10
echo 'vm.swappiness=10' | tee -a /etc/sysctl.conf

echo "Swap setup complete! (2GB Active)"
free -h
