# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# --- ETAPA 2: Frontend (Node.js) ---
# node:20 (Debian full) es más estable en entornos LXC con restricciones de proceso
FROM node:20 AS frontend
WORKDIR /app

RUN npm install -g pnpm

# Copiamos solo lo necesario para instalar dependencias (cache eficiente)
COPY package.json pnpm-lock.yaml* ./

# ESBUILD_BINARY_PATH vacío evita el error ENOTCONN en Proxmox/LXC
# terser se usa como minificador alternativo al binario nativo de esbuild
RUN ESBUILD_BINARY_PATH="" pnpm install --no-frozen-lockfile && pnpm add -D terser

# Copiamos el resto y construimos
COPY . .
RUN ESBUILD_BINARY_PATH="" pnpm build

# --- ETAPA 3: Imagen Final (Producción) ---
FROM php:8.4-fpm-alpine
RUN apk add --no-cache libpng libzip icu oniguruma postgresql-libs shadow

# Instalación de extensiones críticas para Laravel y Postgres
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl

WORKDIR /var/www

# Copiamos el proyecto y los artefactos de las etapas anteriores
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Ajuste de permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data
CMD ["php-fpm"]