FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libicu-dev \
    nodejs npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    zip gd intl bcmath \
    exif pcntl mbstring \
    xml opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Copia el .env correcto antes de cualquier comando artisan
COPY .env.docker .env

RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm ci && npm run build

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]