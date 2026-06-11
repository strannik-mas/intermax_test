sh#!/bin/sh
set -e
mkdir -p /var/www/html/log
chown -R www-data:www-data /var/www/html/log
exec docker-php-entrypoint apache2-foreground