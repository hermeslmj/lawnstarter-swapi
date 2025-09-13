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
php artisan key:generate
```

### 3. Run Migration

```sh
php artisan migrate
```


### 4. How to connect to redis
docker exec -it swapi-redis redis-cli


### 5. How to start manually the queue and schedule
php artisan schedule:work
php artisan queue:work
