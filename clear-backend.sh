#!/usr/bin/env bash

source .env

# Limpar arquivos de log
docker exec -it $CONTAINER_BACK_NAME find storage/logs/ ! -name '.gitignore' -type f -delete

# Limpar caches
docker exec -it $CONTAINER_BACK_NAME php artisan config:clear
docker exec -it $CONTAINER_BACK_NAME php artisan route:clear
docker exec -it $CONTAINER_BACK_NAME php artisan view:clear
docker exec -it $CONTAINER_BACK_NAME php artisan cache:clear
docker exec -it $CONTAINER_BACK_NAME php artisan event:clear

# Limpar sessões armazenadas em arquivos, preservando o .gitignore
docker exec -it $CONTAINER_BACK_NAME find storage/framework/sessions/ ! -name '.gitignore' -type f -delete

docker exec -it $CONTAINER_BACK_NAME composer dump-autoload

echo "Cleared!"
