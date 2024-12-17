# Projeto base

Estrutura para inicialização de um projeto.

## Setup e automações

Abaixo se encontram os passos para SETUP e demais comandos disponíveis.

### Ambiente de desenvolvimento

#### Requisitos:
- Docker

Após o setup, o projeto deve conter os seguintes diretórios:

```text
> src
  | > backend
  | > frontend
  | > database
```

___

### Scripts disponíveis em: `/scripts`

O script abaixo realiza o setup no ambiente de desenvolvimento:

```shell
./setup-dev.sh
```

O script abaixo é utilizado para iniciar o projeto:

```shell
./start.sh
```

O script abaixo é utilizado para parar a execução do projeto:

```sh
./stop.sh
```

Para executar comandos dentro dos containers backend e frontend execute os respectivos scripts
abaixo seguido do comando desejado, como no exemplo: `./container-backend php artisan migrate:status`

```shell
./container-backend [comando]
```

```shell
./container-frontend [comando]
```

---
### Ambiente de Homologação

Passos para setup no ambiente de homologação:

Realize os passos a seguir somente para o repositório principal/infra.
Se o seu projeto possui repositórios individuais para backend e frontend,
eles serão tratados no decorrer do setup. Siga atentamente às instruções.

- Instalação de **Docker** e **Git**
- Criação de chave SSH e adição da chave como _Deploy Key_ no repositório (Ex: GitHub)
  - Criação da chave SSH: `ssh-keygen -t ed25519 -f ~/.ssh/[SEU-REPOSITORIO] -C "" -N ""`
  - Substitua **"[SEU-REPOSITORIO]"** para o nome do seu repositório
  - Exemplo: `ssh-keygen -t ed25519 -f ~/.ssh/sample-project -C "" -N ""`
  - Obter chave pública gerada: `cat ~/.ssh/[SEU-REPOSITORIO].pub`
  - Adicionar a chave pública domo Deploy Key no repositório Git (Ex: GitHub)
- Clone do projeto usando chave SSH privada
  - `git clone -c core.sshCommand="/usr/bin/ssh -i ~/.ssh/[SEU-REPOSITORIO]" [URL-SSH-REPOSITORIO]`
  - Substitua **"[SEU-REPOSITORIO]"** e **"[URL-SSH-REPOSITORIO]"** para o nome e URL do seu repositório
  - Exemplo: `git clone -c core.sshCommand="/usr/bin/ssh -i ~/.ssh/sample-project" git@github.com:user/sample-project.git`
  - Clonando dessa forma as demais operações git do projeto usarão a chave SSH especificada, como `git pull`
  - Para verificar essa configuração SSH entre no diretório do seu projeto `cd [SEU-REPOSITORIO]`, em seguida `git config --get core.sshCommand`
- No diretório do projeto faça as seguintes configurações git
  - `git config pull.rebase true`
  - `git config user.email "email@example.com"` (Não necessita ser um e-mail real se o ambiente não precisar subir código)
  - `git config user.name "Project"`
- Certifique que as variáveis em `.env.example` estão corretamente definidas para os demais repositórios (caso possua)

Após conclusão dos passos acima, o script `setup-homolog.sh` realizará o setup no ambiente de homologação:

```shell
./setup-homolog.sh
```

Após a execução do script `setup-homolog.sh` mais alguns passos podem se fazer necessário para concluir a configuração:

- Configuração no NGINX do servidor para as portas dos containers backend e frontend
- Configuração do .env backend para as credenciais do Banco de dados (Caso necessário)
- Configuração do .env frontend para a URL da API
- Restartar containers (Novo build do container frontend)

### Deploy

O script abaixo pode ser utilizado para realização do deploy no ambiente de homologação,
passando as flags `--all`, `--infra`, `--backend` ou `--frontend` para realizar o deploy do respectivo ambiente:

```shell
deploy.sh --all
```

---

**OBS:** O arquivo `.env` contém variáveis de ambiente usadas no setup, além de valores como credenciais da base de dados,
e portas dos serviços backend, frontend e do banco de dados, que são copiados para os arquivos
`.env` do backend e frontend durante o processo de setup, certifique que esses valores estão correatemnte definidos
nos respectivos arquivos `.env`.

---

### Criando primeiro usuário

Para criar o primeiro usuário execute o script abaixo:

```shell
./create-system-user.sh
```

Ou execute manualmente os 3 comandos a seguir:

```shell
./container-backend php artisan ti

$permission = Permission::create(["name"=>"Permissão total","abilities"=>["users","permissions"]])

User::create(["name"=>"The first","email"=>"email@mail.com","status"=>"active","password"=>bcrypt("projeto"),"role"=>"admin","permission_id"=>$permission->id])
```
