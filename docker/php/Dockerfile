FROM php:7.4-apache
RUN apt-get update && \
    apt install -y zlib1g-dev libpng-dev libjpeg-dev && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ && \
    docker-php-ext-install pdo_mysql gd && \
    a2enmod rewrite