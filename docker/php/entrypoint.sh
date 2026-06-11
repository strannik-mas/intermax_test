#!/bin/sh
mkdir -p /var/www/html/temp /var/www/html/log
chown -R www-data:www-data /var/www/html/temp /var/www/html/log
exec docker-php-entrypoint apache2-foreground