#!/usr/bin/env bash

read -p "Informe o hostname (alias) para seu sistema (exemplo: dev.seuprojeto): " HOSTNAME

if [[ -z "$HOSTNAME" ]]; then
    echo "Hostname não informado. Saindo..."
    exit 1
fi

if grep -q "$HOSTNAME" /etc/hosts; then
  echo "Hostname '$HOSTNAME' já está adicionado!"
  exit 1
fi

sudo bash -c "echo -e '\n127.0.0.1       $HOSTNAME' >> /etc/hosts"
echo "Hostname '$HOSTNAME' adicionado com sucesso!"
