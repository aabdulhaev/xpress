## Содержание

* [Установка и настройка](#установка-и-настройка)
* [Выполнить yii команды через докер](#выполнить-yii-команды-через-докер)
* [Фронт](#фронт)
* [Просмотр логов](#просмотр-логов)
* [Запуск тестов](#запуск-тестов)
* [Документация API Swagger](#документация-api-swagger)

## Установка и настройка
Перед началом разворачивания проекта нужно поставить [Git](https://git-scm.com/book/ru/v1/%D0%92%D0%B2%D0%B5%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-Git), [Node.js](https://nodejs.org/en/), [Docker](https://docs.docker.com/install/)
и [Docker compose](https://docs.docker.com/compose/install/#install-compose).
Клонируем проект через Git (cli) или через какое-нибудь сторонние приложение:

```bash
$ git clone git@gitlab.com:****
```


Настраиваем .env файл. В корне проекта, файл .env.example
Копируем этот файл в .env, и подставляем актуальные данные:
 1) Изменяем `COMPOSE_PROJECT_NAME` на навзание проекта
 2) Изменяем `COMPOSE_PROJECT_TYPE` на нужное нам окружение (development, production)
 2) Изменяем `NGINX_PORT` и `NGINX_PORT_SSL` на свободные порты

```bash
$ cp .env.example .env
```

Настраиваем docker для нужного проекта: 
 1) Изменяем `yii2` на название проекта в файле docker/php/php.ini
 
Инициализируем проект
```bash
$ php init
``` 

Подключаем db в файле `common/config/main-local`
 1) Изменяем dns на настройки из `.env` файла. Пример `'dsn' => 'pgsql:host=yii2_postgres;port=5432;dbname=yii2',`
 2) Изменяем `username` и `password` из файла `.env`. Пример `'username' => 'postgres'` и `'password' => 'superuser'`
 

Поднимаем контенеры в фоне через docker-compose:

```bash
$ docker-compose up -d --build
```

## Выполнить yii команды через докер

Есть два способа выполнить команды:

- снаружи контейнера

```bash
$ docker-compose exec php php yii {дальше уже сами команды}
```

- внутри контейнера

```bash
$ docker-compose exec php bash
```

```bash
$ php yii {дальше уже сами команды}
```

## Фронт

Собрать {frontend} {backend} в прод режиме

```bash
$ npm run build
```

Собрать {frontend} в прод режиме

```bash
$ npm run prod
```

Собрать {backend} в продакшен режиме

```bash
$ npm run admin-prod
```

Собрать {frontend} {backend} в дев режиме

```bash
$ npm run build-dev
```

Собрать {frontend} в дев режиме

```bash
$ npm run dev
```

Собрать {backend} в дев режиме

```bash
$ npm run admin-dev
```

Запустить вотчер для сборки {frontend} в дев режиме

```bash
$ npm run watch
```

Запустить вотчер для сборки {backend} в дев режиме

```bash
$ npm run admin-watch
```

## Просмотр логов

```bash
$ docker-compose logs {название контейнера}
```
Пример `$ docker-compose logs php`

## Запуск тестов
Запуск тестов всех систем:
```bash
$ vendor/bin/codecept run
```

Запуск UNIT тестов:
```bash
$ vendor/bin/codecept run -- -c common
```

Запуск тестов REST API:
```bash
$ vendor/bin/codecept run -- -c api
```

## Документация API Swagger

Сама документация доступна по домену на который привязан API, адрес для документации `/v1/docs` 

Для описания используется [Swagger версии 3.0](https://swagger.io/docs/specification/basic-structure/)   
Примеры аннотаций можно посмотреть по [ссылке](https://github.com/zircote/swagger-php/tree/master/Examples)

При добавлении или изменении API методов обязательно в аннотации к методу добавить описание для swagger
