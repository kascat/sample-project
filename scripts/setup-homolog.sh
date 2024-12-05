#!/usr/bin/env bash

echo "::::: SETUP STARTED :::::"

echo "Atenção:"
echo "- O setup irá criar o arquivo .env a partir de .env.example, e irá abrí-lo para edição automaticamente."
echo "- Se desejar preencher os valores .env previamente, crie o arquivo com 'cp .env.example .env' e modifique-o com 'nano .env'."
echo "- Obs: Se o arquivo .env já existir, ele NÃO será sobrescrito no setup."
echo "- Caso seu projeto possua repositórios individuais backend e frontend, configure-os no .env, e siga as instruções para cada repositório durante o setup."

read -p "Prosseguir com setup? (s/N): " CONTINUE_SETUP

if [[ "$CONTINUE_SETUP" != "s" && "$CONTINUE_SETUP" != "S" ]]; then
    echo "Saindo..."
    exit 1
fi

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

if [ ! -e ".env" ]; then
  cp .env.example .env
fi

nano .env

export $(grep -v '^#' .env | grep -v '^$' | xargs)
# "source .env" necessário para carregar valores com espaço ex: APP_NAME
source .env

pushd src > /dev/null

if [[ $BACK_REPO_GIT ]]; then
  ssh-keygen -t ed25519 -f ~/.ssh/$BACK_REPO_NAME -C "" -N ""
  echo "Adicione a chave pública abaixo (/root/.ssh/$BACK_REPO_NAME.pub) como Deploy Key em seu repositório Git $BACK_REPO_NAME antes de prosseguir"
  cat /root/.ssh/$BACK_REPO_NAME.pub
  read -p "Após adição da Deploy Key, clique qualquer tecla para prosseguir"
  git clone -c core.sshCommand="/usr/bin/ssh -i /root/.ssh/$BACK_REPO_NAME" $BACK_REPO_GIT $BACK_PROJECT_DIR
  git -C $BACK_PROJECT_DIR config pull.rebase true
  git -C $BACK_PROJECT_DIR config user.email "email@example.com"
  git -C $BACK_PROJECT_DIR config user.name "Project"
fi

if [[ $FRONT_REPO_GIT ]]; then
  ssh-keygen -t ed25519 -f ~/.ssh/$FRONT_REPO_NAME -C "" -N ""
  echo "Adicione a chave pública abaixo (/root/.ssh/$FRONT_REPO_NAME.pub) como Deploy Key em seu repositório Git $FRONT_REPO_NAME antes de prosseguir"
  cat /root/.ssh/$FRONT_REPO_NAME.pub
  read -p "Após adição da Deploy Key, clique qualquer tecla para prosseguir"
  git clone -c core.sshCommand="/usr/bin/ssh -i /root/.ssh/$FRONT_REPO_NAME" $FRONT_REPO_GIT $FRONT_PROJECT_DIR
  git -C $FRONT_PROJECT_DIR config pull.rebase true
  git -C $FRONT_PROJECT_DIR config user.email "email@example.com"
  git -C $FRONT_PROJECT_DIR config user.name "Project"
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

cp docker/docker-compose.homolog.yaml docker-compose.yaml

docker compose build --force-rm --no-cache

docker compose up --force-recreate -d

docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/storage
docker exec -it $BACK_CONTAINER_NAME chown -R nginx:nginx /var/www/app/bootstrap/cache
docker exec -it $BACK_CONTAINER_NAME composer install --no-dev
docker exec -it $BACK_CONTAINER_NAME php artisan key:generate
docker restart $BACK_CONTAINER_NAME
docker exec -it $BACK_CONTAINER_NAME php artisan migrate

docker ps

echo ""
echo "Para concluir o setup e configurações do servidor os passos a seguir podem ser necessários:"
echo " - Configuração no NGINX do servidor para as portas dos containers backend e frontend"
echo " - Configuração do .env backend para as credenciais do Banco de dados"
echo " - Configuração do .env frontend para a URL da API"
echo " - Restartar containers (Novo build para container frontend)"

echo "::::: SETUP COMPLETED :::::"
