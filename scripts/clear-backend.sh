#!/usr/bin/env bash

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

export $(grep -v '^#' .env | grep -v '^$' | xargs)

# Limpar arquivos de log
docker exec -it $BACK_CONTAINER_NAME find storage/logs/ ! -name '.gitignore' -type f -delete

# Limpar caches
docker exec -it $BACK_CONTAINER_NAME php artisan config:clear
docker exec -it $BACK_CONTAINER_NAME php artisan route:clear
docker exec -it $BACK_CONTAINER_NAME php artisan view:clear
docker exec -it $BACK_CONTAINER_NAME php artisan cache:clear
docker exec -it $BACK_CONTAINER_NAME php artisan event:clear

# Limpar sessões armazenadas em arquivos, preservando o .gitignore
docker exec -it $BACK_CONTAINER_NAME find storage/framework/sessions/ ! -name '.gitignore' -type f -delete

docker exec -it $BACK_CONTAINER_NAME composer dump-autoload

echo "Cleared!"
