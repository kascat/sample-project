#!/usr/bin/env bash

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

export $(grep -v '^#' .env | grep -v '^$' | xargs)

docker exec -it $BACK_CONTAINER_NAME php artisan ti --execute='Permission::create(["name"=>"Permissão total","abilities"=>["users","permissions"]])'

docker exec -it $BACK_CONTAINER_NAME php artisan ti --execute='User::create(["name"=>"The first","email"=>"email@mail.com","status"=>"active","password"=>bcrypt("projeto"),"role"=>"admin","permission_id"=>Permission::first()->id])'

echo "::::: USER CREATED :::::"
echo "EMAIL: email@mail.com"
echo "PASS: projeto"
