FROM php:8.0-apache

RUN apt-get update
RUN apt-get install -y wget unzip
RUN php -v
RUN wget https://getcomposer.org/installer -O composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
USER www-data
WORKDIR /var/www/html/
RUN wget https://github.com/IPPWorldwide/PlatformsTest/archive/refs/heads/main.zip -O main.zip
RUN unzip main.zip
WORKDIR /var/www/html/PlatformsTest-main

RUN composer install
