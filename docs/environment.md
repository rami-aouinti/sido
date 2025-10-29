# Environment configuration

## Database connection

The application expects a PostgreSQL database provided by the Docker Compose stack. The default connection string is stored in [`.env`](../.env):

```
DATABASE_URL="postgresql://app:app@postgres:5432/app?serverVersion=16&charset=utf8"
```

This string assumes the Docker services expose a `postgres` container with the `app` database, user, and password. Adjust the URL if your Compose overrides these credentials. Doctrine's `serverVersion` parameter must match the PostgreSQL major version that is running in Docker.

## Database and migrations inside the PHP container

Run the following commands from the project root to create the database and execute migrations from within the PHP container:

```
docker compose exec php bin/console doctrine:database:create --if-not-exists
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

The `database:create` command is safe to re-run; it will not recreate an existing schema. The migration command keeps the schema aligned with the Doctrine migrations bundled in the project.
