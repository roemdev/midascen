# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
# Agregamos dependencias necesarias para compilar extensiones en la etapa vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# --- ETAPA 2: Imagen Final (Producción) ---
FROM php:8.4-fpm-alpine

# CORRECCIÓN: Se añade postgresql-dev para poder compilar pdo_pgsql y pgsql
# Se añade libpng-dev para que docker-php-ext-install no falle si requiere compilar algo de GD
RUN apk add --no-cache \
    libpng \
    libpng-dev \
    libzip \
    libzip-dev \
    icu \
    icu-dev \
    oniguruma \
    oniguruma-dev \
    postgresql-libs \
    postgresql-dev \
    shadow

# Ahora la compilación encontrará las librerías de PostgreSQL (libpq-fe.h)
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl

# Limpieza opcional: Eliminar los paquetes -dev para reducir el tamaño de la imagen final
RUN apk del .build-deps || apk del icu-dev libzip-dev oniguruma-dev postgresql-dev libpng-dev

WORKDIR /var/www

# Copiamos el proyecto
COPY . .
# Copiamos las dependencias de la etapa 1
COPY --from=vendor /app/vendor ./vendor

# Ajuste de permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data
CMD ["php-fpm"]