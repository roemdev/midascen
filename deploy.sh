#!/bin/bash
set -e

echo "=============================="
echo " Desplegando Midascen"
echo "=============================="

# Verificar que existe .env.docker
if [ ! -f .env.docker ]; then
    echo "ERROR: No existe .env.docker. Créalo antes de continuar."
    exit 1
fi

echo "[1/7] Bajando contenedores anteriores..."
docker compose down

echo "[2/7] Construyendo imágenes..."
docker compose build --no-cache

echo "[3/7] Levantando base de datos..."
docker compose up -d db
echo "Esperando que PostgreSQL esté listo..."
sleep 5

echo "[4/7] Levantando todos los servicios..."
docker compose up -d

echo "[5/7] Instalando dependencias y compilando assets..."
docker exec midascen_app composer install --no-interaction --prefer-dist --optimize-autoloader
docker exec midascen_app npm ci
docker exec midascen_app npm run build

echo "[6/7] Permisos, migraciones y cache..."
docker exec midascen_app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker exec midascen_app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
docker exec midascen_app php artisan migrate --force
docker exec midascen_app php artisan optimize:clear
docker exec midascen_app php artisan optimize

echo "[7/7] Listo."
echo ""
echo "La app está corriendo en http://$(hostname -I | awk '{print $1}')"
echo ""
echo "Para crear el usuario admin corre:"
echo "  docker exec -it midascen_app php artisan make:filament-user"