#!/bin/bash
set -e

echo "=========================================="
echo "   Midascen Deployment System (PROD)"
echo "=========================================="

# 1. Limpieza de caches locales antes de subir
echo "[1/4] Preparando entorno..."
php artisan clear-compiled --quiet || true

# 2. Build de Docker con optimización
echo "[2/4] Construyendo imágenes (esto puede tardar)..."
docker compose build --pull --no-cache

# 3. Reinicio de servicios
echo "[3/4] Levantando contenedores..."
docker compose up -d --remove-orphans

# 4. Optimizaciones internas de Laravel
echo "[4/4] Ejecutando optimizaciones de Laravel..."
docker exec midascen_app php artisan storage:link || true
docker exec midascen_app php artisan migrate --force
docker exec midascen_app php artisan config:cache
docker exec midascen_app php artisan route:cache
docker exec midascen_app php artisan view:cache
docker exec midascen_app php artisan event:cache

echo ""
echo "------------------------------------------"
echo " Despliegue completado con éxito."
echo " URL: http://$(hostname -I | awk '{print $1}')"
echo "------------------------------------------"