#!/bin/bash
set -e

echo "=============================="
echo " Desplegando Midascen"
echo "=============================="

# Verificar .env.docker
if [ ! -f .env.docker ]; then
    echo "ERROR: No existe .env.docker."
    exit 1
fi

echo "[1/4] Bajando contenedores anteriores..."
docker compose down

echo "[2/4] Construyendo imágenes..."
docker compose build --no-cache

echo "[3/4] Levantando servicios..."
docker compose up -d

echo "[4/4] Optimizando Laravel..."
docker exec midascen_app php artisan optimize:clear
docker exec midascen_app php artisan optimize

echo ""
echo "Listo."
echo "App corriendo en: http://$(hostname -I | awk '{print $1}')"