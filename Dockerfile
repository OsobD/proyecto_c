# Usa una imagen oficial de PHP 8.3 con FPM
FROM php:8.3.16-fpm

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
    ca-certificates \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Instala Node.js 20.x desde NodeSource
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs=20.18.3-1nodesource1 \
    && rm -rf /var/lib/apt/lists/*

# Instala las extensiones de PHP necesarias para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer con versión específica
COPY --from=composer:2.8.5 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos de la aplicación
COPY . .

# Instala las dependencias de Composer (sin --no-dev para incluir pail y otras herramientas de desarrollo)
RUN composer install --no-interaction --optimize-autoloader

# Instala las dependencias de NPM y compila los assets
RUN npm ci && npm run build

# Cambia los permisos de los directorios de almacenamiento y caché
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Crea el script de inicialización directamente en el contenedor
RUN echo '#!/bin/bash\n\
echo "Iniciando contenedor de Laravel..."\n\
\n\
# Primero arregla los permisos (esto se ejecuta después de montar los volúmenes)\n\
echo "Configurando permisos de storage y cache..."\n\
mkdir -p storage/logs storage/framework/{sessions,views,cache}\n\
chown -R www-data:www-data storage bootstrap/cache\n\
chmod -R 775 storage bootstrap/cache\n\
\n\
# Compila assets de Vite si no existen\n\
if [ ! -f "public/build/manifest.json" ]; then\n\
    echo "Compilando assets de Vite..."\n\
    npm run build\n\
else\n\
    echo "Assets de Vite ya compilados"\n\
fi\n\
\n\
# Espera a que MySQL esté disponible\n\
echo "Esperando a que MySQL esté listo..."\n\
until php artisan migrate:status 2>/dev/null || [ $? -eq 1 ]; do\n\
    echo "MySQL no está listo aún, esperando..."\n\
    sleep 2\n\
done\n\
\n\
echo "MySQL está listo!"\n\
\n\
# Genera la clave de aplicación si no existe\n\
if [ ! -f ".env" ]; then\n\
    echo "Creando archivo .env..."\n\
    cp .env.example .env\n\
fi\n\
\n\
# Verifica si APP_KEY está vacío\n\
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then\n\
    echo "Generando APP_KEY..."\n\
    php artisan key:generate --force\n\
else\n\
    echo "APP_KEY ya existe"\n\
fi\n\
\n\
# Limpia cachés ejecutándolos como www-data para evitar problemas de permisos\n\
echo "Limpiando cachés..."\n\
su -s /bin/bash -c "php artisan config:clear" www-data\n\
su -s /bin/bash -c "php artisan cache:clear" www-data\n\
su -s /bin/bash -c "php artisan view:clear" www-data\n\
su -s /bin/bash -c "php artisan route:clear" www-data\n\
\n\
# Ejecuta las migraciones\n\
echo "Ejecutando migraciones..."\n\
php artisan migrate --force\n\
\n\
# Genera la migración de sessions si no existe y SESSION_DRIVER=database\n\
if [ "$SESSION_DRIVER" = "database" ]; then\n\
    echo "Verificando tabla de sesiones..."\n\
    php artisan migrate --force\n\
fi\n\
\n\
# Crea el enlace simbólico para storage\n\
echo "Creando enlace simbólico de storage..."\n\
php artisan storage:link || true\n\
\n\
# Arregla los permisos una última vez antes de iniciar PHP-FPM\n\
echo "Aplicando permisos finales..."\n\
chown -R www-data:www-data storage bootstrap/cache\n\
chmod -R 775 storage bootstrap/cache\n\
\n\
# Inicia PHP-FPM\n\
echo "Iniciando PHP-FPM..."\n\
exec php-fpm' > /usr/local/bin/init.sh \
    && chmod +x /usr/local/bin/init.sh

# Expone el puerto 9000 y arranca el servidor PHP-FPM
EXPOSE 9000
CMD ["/usr/local/bin/init.sh"]