#!/bin/sh
chown -R www-data:www-data /var/www/vendor
chmod -R 755 /var/www/vendor

composer clear-cache
composer install --ignore-platform-req=ext-zip --prefer-dist --no-interaction --optimize-autoloader
php artisan key:generate
php artisan migrate:fresh --seed

chmod -R 777 /var/www/storage && chmod -R 777 /var/www/bootstrap/cache
