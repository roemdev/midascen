#!/bin/bash
set -e

echo "=============================="
echo " Deploy Midascen (PROD)"
echo "=============================="

echo "[1/3] Build limpio..."
docker compose build --no-cache

echo "[2/3] Levantando servicios..."
docker compose up -d

echo "[3/3] Cacheando Laravel..."
docker exec midascen_app php artisan config:cache
docker exec midascen_app php artisan route:cache
docker exec midascen_app php artisan view:cache

echo ""
echo "Listo."
echo "URL: http://$(hostname -I | awk '{print $1}')"