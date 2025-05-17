# Usamos la imagen oficial de PHP con FPM
FROM php:8.4-fpm

# Directorio de trabajo
WORKDIR /var/www

# Instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Instalamos Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos el código
COPY . .

# Instalamos dependencias de PHP / Laravel
RUN composer install --optimize-autoloader --no-dev

# Damos permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponemos el puerto de la aplicación
EXPOSE 9000

# Ejecutamos PHP-FPM
CMD ["php-fpm"]

