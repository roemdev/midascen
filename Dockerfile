# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# --- ETAPA 2: Frontend (Node.js) ---
# Usamos Debian (Slim) para evitar errores de socket ENOTCONN en Proxmox/LXC
FROM node:20-slim AS frontend
WORKDIR /app
RUN npm install -g pnpm

# Copiamos solo lo necesario para instalar dependencias (Cache eficiente)
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install --no-frozen-lockfile

# Copiamos el resto y construimos
COPY . .
RUN pnpm build

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

# Ajuste de permisos profesional
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data
CMD ["php-fpm"]