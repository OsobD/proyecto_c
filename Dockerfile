# Usa una imagen oficial de PHP 8.3 con FPM
FROM php:8.3-fpm

# Instala dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    nodejs \
    npm

# Instala las extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos de la aplicación
COPY . .

# Instala las dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instala las dependencias de NPM y compila los assets
RUN npm install && npm run build

# Cambia los permisos de los directorios de almacenamiento y caché
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expone el puerto 9000 y arranca el servidor PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]