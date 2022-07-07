#!/usr/bin/env bash

# Esse script inicia o loop/fila de scripts que forem enviados para o arquivo pipe
# Exemplo, se rodar "echo 'ls' > pipe" esse script vai pegar o "ls" com o "cat pipe"
# e executar no "eval". E com o "while" o script fica ouvindo até que seja parado.

echo "Iniciando ouvinte de shell script 'pipe'."

while true; do eval "$(cat pipe)"; done
