#!/usr/bin/env bash

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

export $(grep -v '^#' .env | grep -v '^$' | xargs)

read -p "Executar containers em primeiro plano (s/N): " FOREGROUND

if [[ "$FOREGROUND" == "s" || "$FOREGROUND" == "S" ]]; then
  docker compose up --force-recreate
  exit 1
else
  docker compose up --force-recreate -d
fi

docker exec -it $BACK_CONTAINER_NAME composer install
docker exec -it $BACK_CONTAINER_NAME php artisan migrate
#docker exec -it $BACK_CONTAINER_NAME service cron stop
#docker exec -it $BACK_CONTAINER_NAME cron
./scripts/chown-files.sh src/$FRONT_PROJECT_DIR
./scripts/chown-files.sh src/$BACK_PROJECT_DIR
docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache

docker ps
#docker exec -it $BACK_CONTAINER_NAME service cron status

echo ""
echo "Acesse o sistema em: http://localhost:$FRONT_CONTAINER_EXTERNAL_PORT"
echo ""

# Comando abaixo abre o navegador default no linux
#xdg-open http://localhost:$FRONT_CONTAINER_EXTERNAL_PORT
