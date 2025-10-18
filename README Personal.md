git clone <url>
cd eemq_preliminar
git checkout <rama>              # si es necesario
composer install                 # instala dependencias PHP
cp .env.docker .env             # configura entorno
docker-compose build --no-cache # construye contenedores
docker-compose up -d            # levanta contenedores

# Dentro del contenedor, instala dependencias JS y compila:
docker exec -it eemq-app npm install
docker exec -it eemq-app npm run build

# Si necesitas migrar:
docker exec -it eemq-app php artisan migrate
