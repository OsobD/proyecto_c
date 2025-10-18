# 1. Clonar
git clone <url>
cd eemq_preliminar

# 2. Composer (dependencias PHP/Laravel)
composer install

# 3. NPM (dependencias JavaScript)
npm install

# 4. Alpine.js
npm install alpinejs
# (Luego agregar import en resources/js/app.js como mostré arriba)

# 5. Configurar entorno
cp .env.example .env
php artisan key:generate

# 6. Base de datos
php artisan migrate

# 7. Build (desarrollo - deja corriendo)
npm run dev
# O para producción:
npm run build

# 8. Servidor (en otra terminal)
php artisan serve