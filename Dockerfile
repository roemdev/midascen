FROM php:8.4-fpm

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libicu-dev \
    nodejs npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Extensiones de PHP
RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    zip gd intl bcmath \
    exif pcntl mbstring \
    xml opcache

# 3. Traer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Configurar directorio de trabajo
WORKDIR /var/www

# 5. Copiar archivos (Importante: El .dockerignore evitará conflictos)
COPY . .
COPY .env.docker .env

# 6. Instalación de dependencias (Como root para evitar errores EACCES)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm ci && npm run build

# 7. AJUSTE FINAL DE PERMISOS
# Entregamos la propiedad a www-data para que la app funcione correctamente
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]