#!/bin/bash
set -e

echo "=========================================="
echo "   Midascen Deployment System (PROD)"
echo "=========================================="

# ------------------------------------------
# PRE-REQUISITO: Node.js debe estar instalado
# en el HOST (no en Docker) para compilar assets.
# En Proxmox LXC: apt install nodejs npm -y
# ------------------------------------------

# 1. Compilar assets en el HOST antes de construir la imagen
#    (esbuild no puede hacer fork() dentro de Docker en LXC sin privilegios)
echo "[1/5] Compilando assets frontend en el host..."

if ! command -v node &> /dev/null; then
    echo ""
    echo "ERROR: Node.js no está instalado en el host."
    echo "Instálalo con: apt install nodejs npm -y"
    echo "Luego instala pnpm con: npm install -g pnpm"
    exit 1
fi

if ! command -v pnpm &> /dev/null; then
    echo "pnpm no encontrado, instalando..."
    npm install -g pnpm
fi

pnpm install --no-frozen-lockfile
pnpm build

echo "Assets compilados correctamente en public/build"

# 2. Build de Docker (ya sin etapa Node)
echo "[2/5] Construyendo imágenes Docker..."
docker compose build --pull --no-cache

# 3. Reinicio de servicios
echo "[3/5] Levantando contenedores..."
docker compose up -d --remove-orphans

# 4. Esperar a que el contenedor app esté listo
echo "[4/5] Esperando que los contenedores estén listos..."
sleep 5

# 5. Optimizaciones internas de Laravel
echo "[5/5] Ejecutando optimizaciones de Laravel..."
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