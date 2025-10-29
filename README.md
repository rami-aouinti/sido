# Sido Project

## Local Quality Tooling

The project uses the upstream releases of PHPStan, Rector, and PHP-CS-Fixer for static analysis, automated refactoring, and coding standards enforcement.

Install the Composer dependencies and run the following commands to execute all checks:

```bash
composer install
vendor/bin/phpstan analyse --configuration=phpstan.neon
vendor/bin/rector process --dry-run --config=rector.php
vendor/bin/php-cs-fixer fix --dry-run --config=.php-cs-fixer.php
```
# Sido Developer Environment

This project ships with a complete Docker based environment that provides a one-command developer experience for the Symfony API, PostgreSQL database, Mercure hub and the Angular frontend.

## Prerequisites

- Docker and Docker Compose v2+

## Services

`docker-compose.yml` defines the following containers:

- **php**: PHP 8.2 FPM container with Composer dependencies installed.
- **nginx**: Fronts the API and serves traffic on [http://localhost:8080](http://localhost:8080).
- **database**: PostgreSQL 15 instance with persistent storage.
- **mercure**: Real-time hub exposed on [http://localhost:3000](http://localhost:3000).
- **frontend**: Angular development server (expects the Angular project in `./frontend`).

All containers share a dedicated internal network so they can communicate using the service names defined in the compose file.

## First run

```bash
docker compose build
docker compose up -d
```

The build step installs PHP dependencies through Composer and caches the Docker layers for subsequent runs. The `up -d` command starts every service in the background, attaching the containers to the shared network.

### Configure environment variables

The default `.env` file already points `DATABASE_URL` at the PostgreSQL service started by Docker Compose:

```
postgresql://symfony:symfony@database:5432/app?serverVersion=15&charset=utf8
```

No additional changes are required unless you customise the database credentials in `docker-compose.yml`.

### Preparing the database

With the containers running you can create the database and apply the Doctrine migrations directly from inside the PHP container:

```bash
docker compose exec php bin/console doctrine:database:create --if-not-exists
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

### Angular frontend

The Angular frontend service mounts the repository's `frontend` directory at `/app`. Place or clone the Angular project there. On startup the container installs dependencies and launches `npm run start`. If the directory does not contain an Angular project yet, the container will stay idle until you add one.

## Useful commands

Stop all services:

```bash
docker compose down
```

Rebuild after changing PHP extensions or Composer dependencies:

```bash
docker compose build --no-cache php
```

Inspect container logs:

```bash
docker compose logs -f
```

With these commands in place you can bootstrap the entire stack with a single `docker compose up --build` invocation.
