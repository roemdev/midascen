# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# --- ETAPA 2: Imagen Final (Producción) ---
FROM php:8.4-fpm-alpine

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

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl
RUN apk del libpng-dev libzip-dev icu-dev oniguruma-dev postgresql-dev

WORKDIR /var/www

# 1. Copiamos el código y las dependencias
COPY . .
COPY --from=vendor /app/vendor ./vendor

# 2. CREAMOS LAS CARPETAS DE CACHÉ EXPLÍCITAMENTE
# Laravel necesita estas carpetas para que 'package:discover' no falle.
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache

# 3. APLICAMOS PERMISOS ANTES DEL DUMP-AUTOLOAD
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 4. Ahora sí generamos el autoload (esto disparará package:discover sin errores)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev

USER www-data
CMD ["php-fpm"]