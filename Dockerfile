# --- ETAPA 1: PHP Dependencies (Composer) ---
FROM php:8.4-cli-alpine AS vendor
RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# --- ETAPA 2: Imagen Final (Producción) ---
# Los assets (public/build) se compilan en el host antes del deploy.
# Ver deploy.sh — el build de Node corre en el host, no en Docker,
# porque Proxmox LXC sin privilegios bloquea fork()/clone() que esbuild necesita.
FROM php:8.4-fpm-alpine
RUN apk add --no-cache libpng libzip icu oniguruma postgresql-libs shadow

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl

WORKDIR /var/www

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data
CMD ["php-fpm"]