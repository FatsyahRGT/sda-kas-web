#!/bin/bash
set -e

echo "=========================================="
echo "  Deploying SDA Kas Web to VPS Headless   "
echo "  IP: 43.157.243.62                       "
echo "=========================================="

# 1. Pastikan Docker dan Docker Compose terinstall
if ! command -v docker &> /dev/null; then
    echo "==> Menginstall Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
fi

# 2. Build dan jalankan containers
echo "==> Membangun dan menjalankan container..."
docker compose down || true
docker compose build --no-cache
docker compose up -d

echo "==> Menunggu aplikasi aktif..."
sleep 10

# 3. Status container
docker compose ps

echo "=========================================="
echo "  Deployment Berhasil!                    "
echo "  Akses Web: http://43.157.243.62         "
echo "=========================================="
