#!/usr/bin/env bash

SCOPE=${1/--/}
SCOPES=("root" "backend" "frontend" "all")

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

export $(grep -v '^#' .env | grep -v '^$' | xargs)

#NOTIFIER=scripts/slack-notify

if [[ ! " ${SCOPES[@]} " =~ " ${SCOPE} " ]]; then
  echo "Insira um escopo de deploy válido: ('--root', '--backend', '--frontend', '--all')"
  exit 1
fi

docker compose up -d

if [[ $SCOPE == "root" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY ROOT STARTED :::::"
  #$NOTIFIER ":black_small_square: HOMOLOG ROOT - DEPLOY STARTED"
  (git checkout . && git checkout $ROOT_REPO_BRANCH && git pull origin $ROOT_REPO_BRANCH)
  echo "::::: DEPLOY ROOT COMPLETED :::::"
  #$NOTIFIER ":black_small_square: HOMOLOG ROOT - DEPLOY COMPLETED"
fi

if [[ $SCOPE == "backend" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY BACKEND STARTED :::::"
  #$NOTIFIER ":small_orange_diamond: HOMOLOG BACKEND - DEPLOY STARTED"
  [[ $BACK_REPO_GIT ]] && BACK_REPO_BRANCH=$BACK_REPO_BRANCH || BACK_REPO_BRANCH=$ROOT_REPO_BRANCH
  (cd src/$BACK_PROJECT_DIR && git checkout . && git checkout $BACK_REPO_BRANCH && git pull origin $BACK_REPO_BRANCH)
  docker exec -it $BACK_CONTAINER_NAME composer install --no-dev
  docker exec -it $BACK_CONTAINER_NAME php artisan migrate --force
  docker restart $BACK_CONTAINER_NAME
  #docker exec -it $BACK_CONTAINER_NAME service cron stop
  #docker exec -it $BACK_CONTAINER_NAME cron
  docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/storage
  docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache

  docker ps
  #docker exec -it $BACK_CONTAINER_NAME service cron status
  echo "::::: DEPLOY BACKEND COMPLETED :::::"
  #$NOTIFIER ":small_orange_diamond: HOMOLOG BACKEND - DEPLOY COMPLETED"
fi

if [[ $SCOPE == "frontend" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY FRONTEND STARTED :::::"
  #$NOTIFIER ":small_blue_diamond: HOMOLOG FRONTEND - DEPLOY STARTED"
  [[ $FRONT_REPO_GIT ]] && FRONT_REPO_BRANCH=$FRONT_REPO_BRANCH || FRONT_REPO_BRANCH=$ROOT_REPO_BRANCH
  (cd src/$FRONT_PROJECT_DIR && git checkout . && git checkout $FRONT_REPO_BRANCH && git pull origin $FRONT_REPO_BRANCH)
  docker exec -it $FRONT_CONTAINER_NAME yarn
  docker exec -it $FRONT_CONTAINER_NAME quasar build
  echo "::::: DEPLOY FRONTEND COMPLETED :::::"
  #$NOTIFIER ":small_blue_diamond: HOMOLOG FRONTEND - DEPLOY COMPLETED"
fi
