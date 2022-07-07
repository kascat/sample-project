# Projeto base

O projeto base possui a estrutura inicial para inicialização de um projeto.

---

## Setup e automações

Abaixo se encontram os passos para SETUP e demais comandos disponíveis.

### Ambiente de desenvolvimento

OBS: É necessário que já estejam previamente instalados **Docker** e **Docker compose**

A estrutura do projeto após o setup está demonstrada abaixo:

```text
>
  | > backend
  | > frontend
  | > database (caso possua)
```

O comando abaixo realiza o setup no ambiente local:

```shell
./setup-local.sh
```

O comando abaixo é utilizado para iniciar o projeto:

```shell
./start-local.sh
```

O comando abaixo é utilizado para parar a execução do projeto:

```sh
./stop-local.sh
```

Para executar comandos dentro dos containers backend e frontend execute os respectivos scripts
abaixo seguido do comando desejado, como no exemplo: `./container-backend php artisan migrate:status`

```shell
./container-backend [comando]
```

```shell
./container-frontend [comando]
```

O comando abaixo pode ser utilizado para atualizar o backend e frontend com a branch **dev**.

```shell
./git-pull-local.sh
```

---
### Ambiente de Homolog

Para realizar o setup no ambiente de homolog os itens abaixo precisam ser seguidos:

- Instalação de **Docker** e **Docker compose**
- Criação de chaves SSH para operações de cada repositório Git
- Adição no Git das chaves SSH criadas
- Mapeamento dos repositórios para as respectivas chaves SSH em ~/.ssh/config
- Clone dos repositórios backend e frontend (Caso seja repositório individual) dentro do diretório principal

O comando abaixo realiza a configuração dos containers no ambiente de homolog:

```shell
./setup-homolog.sh
```

Após a execução do script `setup-homolog.sh` é necessário realizar mais alguns passos para concluir a configuração:

- Configurar NGINX do servidor para as portas dos containers backend e frontend
- Configurar .env backend para as credenciais do BD (Caso necessário)
- Configurar .env frontend para a URL da API
- Restartar containers

O comando abaixo pode ser utilizado para execução do deploy em homolog,
podendo passar as flags `--all`, `--backend` ou `--frontend` realizando o deploy do respectivo ambiente:

```shell
deploy-homolog.sh --all
```

---

OBS: O arquivo .env contém as credenciais para criação da base de dados, as mesmas credenciais definidas
nesse arquivo devem ser usadas no .env do backend para conexão com a base. Além de variáveis gerais dos containers.

---

### Criando primeiro usuário

Para criar o primeiro usuário da base pode ser executado o script abaixo:

```shell
./first-user.sh
```

Ou execute manualmente os 3 comandos a seguir:

```shell
./container-backend php artisan ti

$permission = Permission::create(["name" => "Administrador"])

User::create(["name"=>"The first","email"=>"email@mail.com","password"=>bcrypt("projeto"),"permission_id"=>$permission->id])
```

---

### Informações gerais

#### Arquivo `pipe`

O arquivo `pipe` na pasta `backend` é um arquivo pipe/FIFO utilizado no projeto para execução de shell script
a partir do php que utiliza o usuário 'nginx', diretamente no container como usuário 'root'.

Para isso o arquivo `exec-pipe.sh` deve ser executado (terminal ou cron) para começar ouvir
os scripts que forem chamados.

Para entender melhor esse processo vide `exec-pipe.sh` e pesquise por "Arquivos FIFO".
