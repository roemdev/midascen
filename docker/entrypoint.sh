#!/bin/sh
set -e

echo "Actualizando desde el repositorio..."
git pull origin main

echo "Instalando dependencias..."
composer install --no-dev --optimize-autoloader

echo "Compilando assets..."
npm ci && npm run build

echo "Corriendo migraciones..."
php artisan migrate --force

echo "Limpiando cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando servicios..."
php-fpm -D
nginx -g 'daemon off;'