#!/bin/bash
set -e

echo "=============================="
echo " Desplegando Midascen"
echo "=============================="

if [ ! -f .env.docker ]; then
    echo "ERROR: No existe .env.docker. Créalo antes de continuar."
    exit 1
fi

echo "[1/7] Bajando contenedores anteriores..."
docker compose down

echo "[2/7] Construyendo imágenes..."
# Al usar build en el Dockerfile, npm ya corre ahí.
docker compose build --no-cache

echo "[3/7] Levantando base de datos..."
docker compose up -d db
sleep 5

echo "[4/7] Levantando todos los servicios..."
docker compose up -d

# ELIMINAMOS EL PASO 5 REDUNDANTE (Ya se hizo en el build)

echo "[6/7] Permisos, migraciones y cache..."
# Aseguramos que los directorios existan antes de dar permisos
docker exec midascen_app mkdir -p storage bootstrap/cache
docker exec midascen_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec midascen_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

docker exec midascen_app php artisan migrate --force
docker exec midascen_app php artisan optimize:clear
docker exec midascen_app php artisan optimize

echo "[7/7] Listo."
echo "La app está corriendo en http://$(hostname -I | awk '{print $1}')"