#!/bin/sh

npm install && npm run build
composer install --ignore-platform-req=ext-zip
php artisan key:generate
php artisan migrate

chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /var/www/node_modules
php-fpm
