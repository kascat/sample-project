#!/usr/bin/env bash

echo "::::: SETUP STARTED :::::"

echo "Antes de prosseguir certifique que os passos a seguir foram executados:"
echo " - Criação de chaves SSH para operações de cada repositório Git"
echo " - Adição das chaves SSH para Deploy no Git"
echo " - Mapeamento dos repositórios para as respectivas chaves SSH em ~/.ssh/config"
echo " - Cópia do arquivo .env.example para .env (da pasta principal) e configuração das variáveis"

read -p "Confirma inicio do setup?"

source .env

if [[ $REPO_GIT_BACK ]]; then
  git clone $REPO_GIT_BACK
fi

if [[ $REPO_GIT_FRONT ]]; then
  git clone $REPO_GIT_FRONT
fi

cp $PROJECT_BACK_DIR/.env.example $PROJECT_BACK_DIR/.env
cp $PROJECT_FRONT_DIR/.env.example $PROJECT_FRONT_DIR/.env

cp docker-compose.override.homolog.yaml docker-compose.override.yaml

docker-compose build --force-rm --no-cache

docker-compose up --force-recreate -d

docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $CONTAINER_BACK_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache
docker exec -it $CONTAINER_BACK_NAME composer install
docker exec -it $CONTAINER_BACK_NAME php artisan key:generate
docker exec -it $CONTAINER_BACK_NAME php artisan migrate

docker ps

echo ""
echo "Para concluir o setup realize os itens a seguir:"
echo " - Configurar NGINX do servidor para as portas dos containers backend e frontend"
echo " - Configurar .env backend para as credenciais do BD (Caso necessário)"
echo " - Configurar .env frontend para a URL da API"
echo " - Restartar containers"

echo "::::: SETUP COMPLETED :::::"
