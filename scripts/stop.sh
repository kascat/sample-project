#!/usr/bin/env bash

ROOT_DIR="$(dirname "$(realpath "$0")")/.."

cd $ROOT_DIR

docker compose down
