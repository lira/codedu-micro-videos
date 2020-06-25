#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
composer install
php artisan key:generate
php artisan migrate
chmod -R 777 storage/

php-fpm
