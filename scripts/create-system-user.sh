#!/usr/bin/env bash

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

export $(grep -v '^#' .env | grep -v '^$' | xargs)

read -p "Nome: " USER_NAME
read -p "E-mail: " USER_EMAIL
read -p "Senha: " USER_PASS

docker exec -it $BACK_CONTAINER_NAME php artisan ti --execute='Permission::firstOrCreate(["id"=>1], ["name"=>"Permissão total","abilities"=>["users","permissions"]])'

docker exec -it $BACK_CONTAINER_NAME php artisan ti --execute='User::create(["name"=>"'"$USER_NAME"'","email"=>"'"$USER_EMAIL"'","status"=>"active","password"=>bcrypt("'"$USER_PASS"'"),"role"=>"admin","permission_id"=>Permission::first()->id])'

echo "::::: USER CREATED :::::"
echo "Nome: $USER_NAME"
echo "E-mail: $USER_EMAIL"
echo "Senha: $USER_PASS"
