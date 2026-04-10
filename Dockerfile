# =========================
# STAGE 1: DEPENDENCIAS PHP
# =========================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader


# =========================
# STAGE 2: BUILD FRONTEND
# =========================
FROM node:20-alpine AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# =========================
# STAGE 3: APP FINAL
# =========================
FROM php:8.4-fpm-alpine

# Dependencias mínimas
RUN apk add --no-cache \
    bash \
    libpng \
    libzip \
    icu \
    oniguruma \
    postgresql-libs

# Extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    bcmath \
    exif \
    pcntl \
    opcache

WORKDIR /var/www

# Copiar app
COPY . .

# Copiar vendor ya optimizado
COPY --from=vendor /app/vendor /var/www/vendor

# Copiar build frontend
COPY --from=frontend /app/public/build /var/www/public/build

# Permisos correctos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

USER www-data

CMD ["php-fpm"]