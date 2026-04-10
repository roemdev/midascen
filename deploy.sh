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

echo "[1/6] Bajando contenedores anteriores..."
docker compose down

echo "[2/6] Construyendo imágenes (esto incluye Composer y NPM)..."
docker compose build --no-cache

echo "[3/6] Levantando base de datos..."
docker compose up -d db
echo "Esperando que PostgreSQL esté listo..."
sleep 5

echo "[4/6] Levantando todos los servicios..."
docker compose up -d

echo "[5/6] Ejecutando tareas internas de Laravel..."
# Migraciones, limpieza y optimización
docker exec midascen_app php artisan migrate --force
docker exec midascen_app php artisan optimize:clear
docker exec midascen_app php artisan optimize

echo "[6/6] Listo."
echo ""
echo "La app está corriendo en http://$(hostname -I | awk '{print $1}')"
echo ""
echo "Para crear el usuario admin corre:"
echo "  docker exec -it midascen_app php artisan make:filament-user"