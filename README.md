# Overview
This project serves as a technical exam for Legal One.

# Setup

### Environment Variables
> ⚠️ Before anything else, you need to setup your .env file first. Both [Docker Compose](https://docs.docker.com/compose/) and the project needs it in order to work properly 

First, copy the `.env` file to yours:

```shell
cp .env .env.local
```

then in your `.env.local` file, supply the following environment variables:

1. `POSTGRES_USER`
2. `POSTGRES_PASSWORD` 
3. `POSTGRES_DB` 
4. `SERVER_NAME` 

After that, you can proceed with the next steps. 

### Docker Compose
This project uses [Docker Compose](https://docs.docker.com/compose/) for development, and can be started up by simply running the following:

```shell
docker compose --env-file ./.env.local build
docker compose --env-file ./.env.local up -d
``` 

### Install dependencies
Once the containers are up and running, you can now install the dependencies by entering the `php` container:
```shell
docker compose exec -it php /bin/bash
```

and installing the [Composer](https://getcomposer.org/) packages:

```shell
composer install
```

### Database
#### Create the test database
Once you have installed the [Composer](https://getcomposer.org/) packages, you can start creating the test database for the unit tests to work.
```shell
php bin/console doctrine:database:create --env=test
```
> 💡️ Since our compose file already created our local database for us, we won't have to run this command for it.

Then, we can start running the migrations

```shell
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:migrate --env=test
```

# Unit Tests
Tests can be run by entering the `php` container again:

```shell
docker compose exec -it php /bin/bash
```

and running:

```shell
php bin/phpunit
```

# Usage
The page can be accessed at the `SERVER_NAME` environment variable you supplied in your `.env.local` file.

Find out more about how this command is used [here](./docs/index.md).
