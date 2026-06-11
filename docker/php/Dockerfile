FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mysqli intl zip

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/html/www!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN mkdir -p /var/www/html/temp /var/www/html/log \
    && chmod -R 777 /var/www/html/temp /var/www/html/log

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html