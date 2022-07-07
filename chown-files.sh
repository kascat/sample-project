#!/usr/bin/env bash

PASSWORD=`cat sudo-pass`

if [[ ! $PASSWORD ]]; then
  read -s -p '[sudo] senha:' PASSWORD
  echo

  read -p "Lembrar senha? (s/n):" REMEMBER
  if [[ $REMEMBER == 's' || $REMEMBER == 'S' ]]; then
    echo $PASSWORD > sudo-pass
    echo 'Senha salva!'
  fi
fi

DIR=$1

if [[ ! $DIR ]]; then
  DIR=.
fi

# Concede permissão
echo $PASSWORD | sudo -S chown -R $USER:$USER $DIR
# Deleta a última linha do comando acima
echo -e "\033[2K"
