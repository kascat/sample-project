#!/usr/bin/env bash

source .env

docker exec -it $CONTAINER_BACK_NAME php artisan ti --execute='Permission::create(["name"=>"Permissão total","abilities"=>["users","permissions"]])'

docker exec -it $CONTAINER_BACK_NAME php artisan ti --execute='User::create(["name"=>"The first","email"=>"email@mail.com","status"=>"active","password"=>bcrypt("projeto"),"role"=>"admin","permission_id"=>Permission::first()->id])'

echo "::::: USER CREATED :::::"
echo "EMAIL: email@mail.com"
echo "PASS: projeto"
