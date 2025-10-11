#!/bin/bash
# Instalar PHP y Composer en el ambiente de Jules
apt-get update
apt-get install -y php php-cli php-mbstring php-xml unzip
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer