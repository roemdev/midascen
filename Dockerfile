FROM php:8.4-fpm

# 1. Instalación de dependencias del sistema (combinadas para reducir capas)
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libpng-dev \
    libonig-dev libxml2-dev libicu-dev \
    nodejs npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Instalación de extensiones PHP
RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    zip gd intl bcmath \
    exif pcntl mbstring \
    xml opcache

# 3. Traer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Configurar directorio de trabajo
WORKDIR /var/www

# 5. Copiar archivos del proyecto
COPY . .
COPY .env.docker .env

# 6. Corregir permisos antes de instalar (Evita errores de ejecución)
RUN chown -R www-data:www-data /var/www

# 7. Instalación de dependencias de PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 8. SOLUCIÓN AL ERROR EACCES: 
# Configuramos npm para que permita ejecutar scripts de post-instalación como root
RUN npm config set user root && npm ci && npm run build

# 9. Ajuste final de permisos para Laravel
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www

CMD ["php-fpm"]