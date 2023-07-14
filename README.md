# Quiz-cms
### [Video review](https://youtu.be/oYBme66Vf6U)

## Technologies
* PHP 8.1
* [Laravel 10](https://laravel.com)
* [Docker](https://www.docker.com/)
* [Docker-compose](https://docs.docker.com/compose/)
* [CORS](https://developer.mozilla.org/ru/docs/Web/HTTP/CORS)
* [PhpUnit](https://phpunit.de/)
* Filament
* MySQL 8.0


## Install

The following sections describe dockerized environment.

Just keep versions of installed software to be consistent with the team and production environment (see [Pre-requisites](#pre-requisites) section).


Set your .env vars:
```bash
cp .env.example .env
```

Emails processing .env settings (you can use [mailtrap](https://mailtrap.io/) or your smtp credentials like user@gmail.com):
```dotenv
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_USERNAME=<mailtrap_key>
MAIL_PASSWORD=<mailtrap_password>
MAIL_PORT=587
MAIL_FROM_ADDRESS=admin@thread.com
MAIL_FROM_NAME="BSA Thread Admin"
```

Install composer dependencies and start local serve
Application server should be ready on `http://localhost:<APP_PORT>`
```bash
composer install
alias sail='vendor/bin/sail'
sail up -d
```
or
```bash
./vendor/bin/sail up
```

Generate app key:
```bash
docker exec -it quiz-cms php artisan key:generate
```

Database migrations install (set proper .env vars)
```bash
docker exec -it quiz-cms php artisan migrate
docker exec -it quiz-cms php artisan db:seed
```

Setting library
```bash
docker exec -it quiz-cms npm i
docker exec -it quiz-cms npm run build
```

You may create a new user account using:
```bash
docker exec -it quiz-cms php artisan make:filament-user
```
Visit your admin panel at /admin to sign in

## Laravel IDE Helper

For ease of development, you can run the data generation function for the Laravel IDE Helper.
```bash
docker exec -it quiz-cms php artisan ide-helper:generate
docker exec -it quiz-cms php artisan ide-helper:models -N
docker exec -it quiz-cms php artisan ide-helper:meta
```

## Debugging

To debug the application we highly recommend you to use xDebug, it is already pre-installed in dockerized environment, but you should setup your IDE.

If permission error
```bash
docker exec -it quiz-cms chmod -R 777 storage
```

You can debug your app with [Telescope](https://laravel.com/docs/10.x/telescope) tool which is installed already
