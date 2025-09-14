# LawnStarter SWAPI Backend

## Getting Started

### 1. Start the Docker Compose Services

```sh
docker compose -f compose.dev.yaml up -d
```

### 2. Install Laravel Dependencies (don't copy and paste all commands.)

```sh
docker compose -f compose.dev.yaml exec workspace bash
composer install
```

### 3. Run Migration

```sh
php artisan migrate
```

## Usefull commands

### 1. How to connect to redis
```sh
docker exec -it swapi-redis redis-cli
```
### 2. How to start manually the queue and schedule
```sh
php artisan schedule:work
php artisan queue:work
```
### 3. Connect to postgres
```sh
docker exec -it swapi-postgres bash
psql -U laravel -d app
```