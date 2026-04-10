# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# CORRECCIÓN: Agregamos --no-scripts para evitar que falle al no encontrar 'artisan'
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# --- ETAPA 2: Imagen Final (Producción) ---
FROM php:8.4-fpm-alpine

# Instalación de dependencias de ejecución y compilación
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

# Compilación de extensiones de PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl

# Limpieza de paquetes de desarrollo para reducir espacio
RUN apk del libpng-dev libzip-dev icu-dev oniguruma-dev postgresql-dev

WORKDIR /var/www

# Copiamos todo el código primero
COPY . .

# Copiamos la carpeta vendor desde la etapa anterior
COPY --from=vendor /app/vendor ./vendor

# Ahora que ya existe 'artisan' y todo el código, generamos el autoload final con scripts
# Esto ejecutará package:discover correctamente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev

# Ajuste de permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data
CMD ["php-fpm"]