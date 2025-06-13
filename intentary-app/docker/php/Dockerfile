# docker/php/Dockerfile

FROM php:8.3-fpm-alpine
# NO debería haber NINGUNA línea en blanco encima de la línea FROM.
# Y asegúrate de que no haya espacios al final de la línea 'alpine'

# Instalar dependencias del sistema
RUN apk update && apk add --no-cache \
    nginx \
    mysql-client \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    oniguruma-dev \
    gmp-dev \
    libpq \
    build-base \
    imagemagick-dev \
    libc-dev

# Instalar extensiones de PHP
# Se recomienda usar docker-php-ext-install para las extensiones base de PHP
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    opcache \
    mbstring \
    tokenizer \
    xml \
    json \
    bcmath \
    gmp \
    pcntl \
    pdo_pgsql # Si usas PostgreSQL

# Instalar extensiones de PHP desde PECL
# Para GD:
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd

# Para Imagick: (si lo necesitas para manipulación de imágenes)
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el usuario y grupo para la aplicación (opcional, pero buena práctica)
# UID 1000 es común para usuarios que no son root
RUN addgroup -g 1000 laraveluser && adduser -u 1000 -G laraveluser -D laraveluser

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Ajustar las configuraciones de PHP para FPM (opcional, pero puede ayudar)
COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Exponer el puerto de FPM (aunque Docker Compose lo manejará internamente)
EXPOSE 9000

# Comando por defecto para iniciar PHP-FPM
CMD ["php-fpm"]