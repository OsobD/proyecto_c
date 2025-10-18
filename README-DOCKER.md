# Guía de uso de Docker para EEMQ

## Requisitos previos
- Docker Desktop instalado
- Docker Compose instalado

## Configuración inicial

1. **Copia el archivo de configuración de Docker:**
```bash
cp .env.docker .env
```

2. **Construye e inicia los contenedores:**
```bash
docker-compose up -d --build
```

3. **Verifica que todos los contenedores estén corriendo:**
```bash
docker-compose ps
```

Deberías ver 3 contenedores: `eemq-app`, `eemq-nginx` y `eemq-db`

## Acceso a la aplicación

- **URL de la aplicación:** http://localhost:8000
- **Base de datos MySQL:** localhost:3306
  - Usuario: `eemq_user`
  - Contraseña: `secret`
  - Base de datos: `eemq_db`

## Comandos útiles

### Ver logs de los contenedores
```bash
# Todos los contenedores
docker-compose logs -f

# Solo la aplicación
docker-compose logs -f app

# Solo Nginx
docker-compose logs -f nginx

# Solo MySQL
docker-compose logs -f db
```

### Ejecutar comandos de Artisan
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan make:model NombreModelo
```

### Ejecutar comandos de Composer
```bash
docker-compose exec app composer install
docker-compose exec app composer require paquete/nombre
```

### Ejecutar comandos de NPM
```bash
docker-compose exec app npm install
docker-compose exec app npm run dev
```

### Acceder al contenedor de la aplicación
```bash
docker-compose exec app bash
```

### Acceder a MySQL
```bash
docker-compose exec db mysql -u eemq_user -psecret eemq_db
```

### Detener los contenedores
```bash
docker-compose down
```

### Detener y eliminar volúmenes (⚠️ esto borra la base de datos)
```bash
docker-compose down -v
```

### Reconstruir los contenedores
```bash
docker-compose up -d --build --force-recreate
```

## Solución de problemas

### Error: "No se puede conectar a la base de datos"
1. Verifica que el contenedor de MySQL esté corriendo: `docker-compose ps`
2. Revisa los logs de MySQL: `docker-compose logs db`
3. Espera unos segundos más, MySQL tarda en inicializarse completamente

### Error: "Permission denied" en storage o bootstrap/cache
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Error: "APP_KEY not set"
```bash
docker-compose exec app php artisan key:generate
```

### Limpiar caché
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## Desarrollo con Livewire

Livewire funciona sin problemas en este setup de Docker. Para desarrollo en tiempo real:

1. **Ejecuta Vite en modo desarrollo:**
```bash
docker-compose exec app npm run dev
```

2. **Si necesitas hot reload, modifica `vite.config.js` para incluir:**
```javascript
server: {
    hmr: {
        host: 'localhost',
    },
}
```

## Notas importantes

- Los datos de MySQL se guardan en un volumen Docker (`mysql_data`), por lo que persisten entre reinicios
- Los archivos `vendor` y `node_modules` también usan volúmenes para mejor rendimiento
- El script de inicialización ([docker/init.sh](docker/init.sh)) se ejecuta automáticamente al iniciar el contenedor y:
  - Espera a que MySQL esté listo
  - Genera APP_KEY si no existe
  - Limpia cachés
  - Ejecuta migraciones automáticamente
  - Crea el enlace simbólico de storage

## Versiones utilizadas

- PHP: 8.3.16
- MySQL: 8.0.40
- Nginx: 1.27.3-alpine
- Composer: 2.8.5

Todas las versiones están especificadas sin usar `latest` para asegurar reproducibilidad.
