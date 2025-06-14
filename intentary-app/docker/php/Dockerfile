# docker/php/Dockerfile

FROM php:8.3-fpm 

# Instalar dependencias del sistema para Debian
RUN apt-get update && apt-get install -y \
    nginx \
    default-libmysqlclient-dev \
    curl \
    git \
    zip \
    unzip \
    libzip4 \
    libzip-dev \
    pkg-config \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype-dev \
    libonig-dev \
    libgmp-dev \
    libxml2-dev \
    libpq-dev \
    libmagickwand-dev \
    build-essential \
    procps \
    nano \
    vim \
    re2c \
    flex \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP UNA POR UNA para depurar
# Si una falla, el build se detendrá justo después de ella.

# Bloque 1 (las que suelen ser muy estables)
RUN docker-php-ext-install -j1 pdo_mysql
RUN docker-php-ext-install -j1 mysqli
RUN docker-php-ext-install -j1 opcache
RUN docker-php-ext-install -j1 mbstring
RUN docker-php-ext-install -j1 pcntl

# Bloque 2 (las que a veces tienen dependencias más finas)
RUN docker-php-ext-install -j1 xml
RUN docker-php-ext-install -j1 gmp
RUN docker-php-ext-install -j1 bcmath

# Configurar e instalar ZIP (requiere configuración especial)
RUN docker-php-ext-configure zip
RUN docker-php-ext-install -j1 zip

# NOTA: tokenizer ya viene habilitado por defecto en PHP 8.3-fpm
# No es necesario instalarlo manualmente

# (Si la necesitas y no la habías quitado)
# RUN docker-php-ext-install -j1 pdo_pgsql

# Instalar extensiones de PHP desde PECL
# Para GD:
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j1 gd

# Para Imagick: (si lo necesitas para manipulación de imágenes)
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el usuario y grupo para la aplicación (opcional, pero buena práctica)
RUN usermod -u 1000 www-data

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Ajustar las configuraciones de PHP para FPM (opcional, pero puede ayudar)
COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Exponer el puerto de FPM (aunque Docker Compose lo manejará internamente)
EXPOSE 9000

# Comando por defecto para iniciar PHP-FPM
CMD ["php-fpm"]