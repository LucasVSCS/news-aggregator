#!/bin/bash
# setup.sh - Script de inicialização

echo "Starting setup..."

docker-compose up -d
docker-compose exec php-fpm composer install
docker-compose exec php-fpm php artisan key:generate
touch database/database.sqlite
docker-compose exec php-fpm php artisan migrate
docker-compose exec php-fpm php artisan db:seed --class=SourceSeeder

echo "Setup complete!"
echo "App: http://localhost:61000"
echo "Horizon: http://localhost:61000/horizon"
