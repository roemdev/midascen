# =========================
# STAGE 1: FRONTEND BUILD
# =========================
FROM node:20 AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# =========================
# STAGE 2: PHP APP
# =========================
FROM php:8.4-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    zip gd intl bcmath \
    exif pcntl mbstring \
    xml opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar proyecto
COPY . .
COPY .env.docker .env

# IMPORTANTE: evitar basura del host
RUN rm -rf node_modules

# Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copiar build del frontend
COPY --from=node_builder /app/public/build /var/www/public/build

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]