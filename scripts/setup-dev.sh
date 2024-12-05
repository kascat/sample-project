#!/usr/bin/env bash

echo "::::: SETUP STARTED :::::"

echo "Antes de prosseguir realize a configuração das variáveis no arquivo .env.example (da pasta principal)."

read -p "Confirma inicio do setup? ('Ctrl + C' pata cancelar)"

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

cp .env.example .env

export $(grep -v '^#' .env | grep -v '^$' | xargs)
# "source .env" necessário para carregar valores com espaço ex: APP_NAME
source .env

pushd src > /dev/null

if [[ $BACK_REPO_GIT ]]; then
  git clone $BACK_REPO_GIT $BACK_PROJECT_DIR
fi

if [[ $FRONT_REPO_GIT ]]; then
  git clone $FRONT_REPO_GIT $FRONT_PROJECT_DIR
fi

cp $BACK_PROJECT_DIR/.env.example $BACK_PROJECT_DIR/.env
sed -i "s|@APP_NAME|$APP_NAME|;" $BACK_PROJECT_DIR/.env
sed -i "s|@FRONT_URL|$FRONT_URL|;" $BACK_PROJECT_DIR/.env
sed -i "s|@DB_HOST|$DB_CONTAINER_NAME|;" $BACK_PROJECT_DIR/.env
sed -i "s|@DB_DATABASE|$DB_DATABASE|;" $BACK_PROJECT_DIR/.env
sed -i "s|@DB_USER|$DB_USER|;" $BACK_PROJECT_DIR/.env
sed -i "s|@DB_PASSWORD|$DB_PASSWORD|;" $BACK_PROJECT_DIR/.env
sed -i "s|@FRONT_PORT|$FRONT_CONTAINER_EXTERNAL_PORT|;" $BACK_PROJECT_DIR/.env

cp $FRONT_PROJECT_DIR/.env.example $FRONT_PROJECT_DIR/.env
sed -i "s|@APP_ID|$APP_ID|;" $FRONT_PROJECT_DIR/.env
sed -i "s|@APP_NAME|$APP_NAME|;" $FRONT_PROJECT_DIR/.env
sed -i "s|@API_PORT|$BACK_CONTAINER_EXTERNAL_PORT|;" $FRONT_PROJECT_DIR/.env
sed -i "s|@FRONT_PORT|$FRONT_CONTAINER_EXTERNAL_PORT|;" $FRONT_PROJECT_DIR/.env
sed -i "s|@FRONT_URL|$FRONT_URL|;" $FRONT_PROJECT_DIR/.env
sed -i "s|@BACK_URL|$BACK_URL|;" $FRONT_PROJECT_DIR/.env

popd > /dev/null

cp docker/docker-compose.development.yaml docker-compose.yaml

docker compose build --force-rm --no-cache

docker compose up --force-recreate -d

docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache
docker exec -it $BACK_CONTAINER_NAME composer install
docker exec -it $BACK_CONTAINER_NAME php artisan key:generate
docker restart $BACK_CONTAINER_NAME
docker exec -it $BACK_CONTAINER_NAME php artisan migrate

docker ps

echo "::::: SETUP COMPLETED :::::"
