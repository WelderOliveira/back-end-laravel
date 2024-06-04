#!/bin/sh

npm install && npm run build
composer install --ignore-platform-req
php artisan key:generate
php artisan migrate:fresh --seed

chmod -R 777 /var/www/storage && chmod -R 777 /var/www/bootstrap/cache
php-fpm
