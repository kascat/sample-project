#!/usr/bin/env bash

echo "::::: SETUP STARTED :::::"

#cp .env.example .env

echo "Antes de prosseguir realize a cópia do arquivo .env.example para .env (da pasta principal) e configure adequadamente as variáveis."

read -p "Confirma inicio do setup? ('Ctrl + C' pata cancelar)"

source .env

if [[ $REPO_GIT_BACK ]]; then
  git clone $REPO_GIT_BACK
fi

if [[ $REPO_GIT_FRONT ]]; then
  git clone $REPO_GIT_FRONT
fi

cp $PROJECT_BACK_DIR/.env.example $PROJECT_BACK_DIR/.env
sed -i "s|@DB_HOST|$CONTAINER_DB_NAME|;" $PROJECT_BACK_DIR/.env
sed -i "s|@DB_DATABASE|$CONTAINER_DB_DATABASE|;" $PROJECT_BACK_DIR/.env
sed -i "s|@DB_PASSWORD|$CONTAINER_DB_PASSWORD|;" $PROJECT_BACK_DIR/.env
sed -i "s|@FRONT_PORT|$CONTAINER_FRONT_EXTERNAL_PORT|;" $PROJECT_BACK_DIR/.env

cp $PROJECT_FRONT_DIR/.env.example $PROJECT_FRONT_DIR/.env
sed -i "s|@API_PORT|$CONTAINER_BACK_EXTERNAL_PORT|;" $PROJECT_FRONT_DIR/.env

cp docker-compose.override.development.yaml docker-compose.override.yaml

docker-compose build --force-rm --no-cache

docker-compose up --force-recreate -d

docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache
docker exec -it $CONTAINER_BACK_NAME composer install
docker exec -it $CONTAINER_BACK_NAME php artisan key:generate
docker restart $CONTAINER_BACK_NAME
docker exec -it $CONTAINER_BACK_NAME php artisan migrate

docker ps

echo "::::: SETUP COMPLETED :::::"

./start-local.sh
