#!/usr/bin/env bash

SCOPE=${1/--/}
SCOPES=("root" "backend" "frontend" "all")

source .env

NOTIFIER=slack-notify

if [[ ! " ${SCOPES[@]} " =~ " ${SCOPE} " ]]; then
  echo "Insira um escopo de deploy válido: ('--root', '--backend', '--frontend', '--all')"
  exit 1
fi

if [[ $SCOPE == "root" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY ROOT STARTED :::::"
  $NOTIFIER ":black_small_square: HOMOLOG ROOT - DEPLOY STARTED"
  (git checkout . && git checkout $REPO_BRANCH_ROOT && git pull origin $REPO_BRANCH_ROOT)
  echo "::::: DEPLOY ROOT COMPLETED :::::"
  $NOTIFIER ":black_small_square: HOMOLOG ROOT - DEPLOY COMPLETED"
fi

if [[ $SCOPE == "backend" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY BACKEND STARTED :::::"
  $NOTIFIER ":small_orange_diamond: HOMOLOG BACKEND - DEPLOY STARTED"
  [[ $REPO_GIT_BACK ]] && REPO_BRANCH_BACK=$REPO_BRANCH_BACK || REPO_BRANCH_BACK=$REPO_BRANCH_ROOT
  (cd $PROJECT_BACK_DIR && git checkout . && git checkout $REPO_BRANCH_BACK && git pull origin $REPO_BRANCH_BACK)
  docker exec -it $CONTAINER_BACK_NAME composer install
  docker exec -it $CONTAINER_BACK_NAME php artisan migrate --force
  docker restart $CONTAINER_BACK_NAME
  docker exec -it $CONTAINER_BACK_NAME service cron stop
  docker exec -it $CONTAINER_BACK_NAME cron
  docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/storage
  docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache

  # docker exec -it $CONTAINER_BACK_NAME mkfifo /var/www/app/pipe
  # docker exec -it $CONTAINER_BACK_NAME chown nginx:nginx /var/www/app/pipe
  # docker exec -d -it $CONTAINER_BACK_NAME sh /var/www/app/exec-pipe.sh

  docker ps
  docker exec -it $CONTAINER_BACK_NAME service cron status
  echo "::::: DEPLOY BACKEND COMPLETED :::::"
  $NOTIFIER ":small_orange_diamond: HOMOLOG BACKEND - DEPLOY COMPLETED"
fi

if [[ $SCOPE == "frontend" || $SCOPE == "all" ]]; then
  echo "::::: DEPLOY FRONTEND STARTED :::::"
  $NOTIFIER ":small_blue_diamond: HOMOLOG FRONTEND - DEPLOY STARTED"
  [[ $REPO_GIT_FRONT ]] && REPO_BRANCH_FRONT=$REPO_BRANCH_FRONT || REPO_BRANCH_FRONT=$REPO_BRANCH_ROOT
  (cd $PROJECT_FRONT_DIR && git checkout . && git checkout $REPO_BRANCH_FRONT && git pull origin $REPO_BRANCH_FRONT)
  docker exec -it $CONTAINER_FRONT_NAME yarn
  docker exec -it $CONTAINER_FRONT_NAME quasar build
  echo "::::: DEPLOY FRONTEND COMPLETED :::::"
  $NOTIFIER ":small_blue_diamond: HOMOLOG FRONTEND - DEPLOY COMPLETED"
fi
