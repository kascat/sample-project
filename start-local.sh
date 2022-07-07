#!/usr/bin/env bash

source .env

docker-compose up --force-recreate -d

docker exec -it $CONTAINER_BACK_NAME composer install
docker exec -it $CONTAINER_BACK_NAME php artisan migrate
docker exec -it $CONTAINER_BACK_NAME service cron stop
docker exec -it $CONTAINER_BACK_NAME cron
./chown-files.sh $PROJECT_DB_DIR
./chown-files.sh $PROJECT_FRONT_DIR
./chown-files.sh $PROJECT_BACK_DIR
docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache

# Arquivo pipe utilizado pela Classe LibreOffice.php
#docker exec -it $CONTAINER_BACK_NAME mkfifo /var/www/app/pipe
#docker exec -it $CONTAINER_BACK_NAME chown nginx:nginx /var/www/app/pipe
#docker exec -d -it $CONTAINER_BACK_NAME sh /var/www/app/exec-pipe.sh

docker ps
docker exec -it $CONTAINER_BACK_NAME service cron status

echo ""
echo "Acesse o sistema em: http://localhost:$CONTAINER_FRONT_EXTERNAL_PORT"
echo ""

# Comando abaixo abre o navegador default no linux
#xdg-open http://localhost:$CONTAINER_FRONT_EXTERNAL_PORT
