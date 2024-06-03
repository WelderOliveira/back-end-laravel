# DESAFIO - BACKEND - TEST
- Realize o Clone deste repositório:
```shell
git clone https://github.com/WelderOliveira/back-end-laravel.git
```
- Acesse o diretório
```shell
cd back-end-laravel
```
- Crie `.env` com base no `.env.example`
```shell
cp .env.example .env
```
## Execução do projeto utilizando Docker

- Build os containers
```shell
docker-compose up --build -d
```

- Verificar os container “app”, “mysql” e "app-nginx" foram criados
```shell
docker ps
```

- Acessar a url
```shell
localhost:8989
```

- Acessar ao container
```shell
docker exec -it app /bin/bash
```

- Rodar testes
```shell
./vendor/bin/phpunit
```

- Rodar Pint (verificação de padrão de código, PSR-12)
```shell
./vendor/bin/pint
```
## Execução do projeto sem utilizar o Docker
#### - Valide as técnologia mínimas exigidas informadas

- Execute o Composer
```shell
composer install
```

- Gerar chave da aplicação
```shell
php artisan key:generate
```

---
- OBS: Caso não queira utilizar o banco padrão (SQLite), ajuste as configurações do seu banco no arquivo .env
---

- Crie as tabelas de persistência
```shell
php artisan migrate
```

- Iniciar o Servidor de Desenvolvimento
```shell
php artisan serve
```

- Acessar a url
```shell
localhost:8000
```

### Tecnologias

- PHP: 8.2
- Laravel: 11.0
- Composer: 2.7.6

#### COVERAGE
- Caso queira verificar o Coverage dos testes

- Crie uma pasta na raíz do projeto com nome coverage-reports

Utilize esse comando dentro do container para instalar o xdebug:
```shell
pecl install xdebug
```

Execute esse comando:

```shell
XDEBUG_MODE=coverage php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-html coverage-reports
```

Abra o arquivo coverage-reports/index.html no navegador de sua preferência


### Endpoints

```shell
{{url}} = <server-url>:<port>/api
```

- [X] Get Account 🚀

    ```shell
    GET: {{url}}/account/2
    ```

- [X] Create Transaction 🚀

    ```shell
    POST: {{url}}/transfer
  
  {
  "value": 120.0,
  "payer": 8,
  "payee": 7
    }
    ```

- [X] Get Extract 🚀

    ```shell
    GET : {{url}}/extract/2
    ```
  
