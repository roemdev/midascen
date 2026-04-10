FROM php:8.4-cli-alpine AS vendor

RUN apk add --no-cache icu-dev libzip-dev oniguruma-dev git zip unzip
RUN docker-php-ext-install intl zip mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader


FROM node:20 AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build


FROM php:8.4-fpm-alpine

RUN apk add --no-cache libpng libzip icu oniguruma postgresql-libs
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring bcmath exif pcntl opcache intl

WORKDIR /var/www

COPY . .
COPY --from=vendor /app/vendor /var/www/vendor
COPY --from=frontend /app/public/build /var/www/public/build

RUN chown -R www-data:www-data /var/www && chmod -R 775 storage bootstrap/cache

USER www-data

CMD ["php-fpm"]