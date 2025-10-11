# Comandos de Configuración del Entorno

Este archivo documenta los comandos utilizados para configurar el entorno de desarrollo en una máquina virtual basada en Debian/Ubuntu.

## 1. Instalar Composer y PHP

El siguiente comando instala `composer`, `php-cli`, y las extensiones de PHP necesarias para este proyecto Laravel (`dom` y `xml`).

```bash
sudo apt-get update && sudo apt-get install -y composer php-cli php-dom php-xml
```

## 2. Instalar Dependencias del Proyecto

Una vez que `composer` está instalado, puedes instalar las dependencias de Laravel y del frontend con los siguientes comandos:

```bash
composer install
npm install && npm run build
```

## 3. Configurar Laravel

Finalmente, copia el archivo de entorno y genera la clave de la aplicación:

```bash
cp .env.example .env
php artisan key:generate
```
