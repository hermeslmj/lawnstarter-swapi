# LawnStarter SWAPI

## Getting Started

The backend project is where all the docker configuration are located, we have compose.dev.yaml and compose.prd.yaml that contains configuration for each environment. 
To start the containers follow the instructions
All the docker config was based on official config from [docker for laravel](https://github.com/dockersamples/laravel-docker-examples) 
### 1. Start the Docker Compose Services

```sh
docker compose -f compose.dev.yaml up -d
```

### 2. Install Laravel Dependencies (don't copy and paste all commands.)

```sh
docker compose -f compose.dev.yaml exec workspace bash
```
```sh
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

## Containers and their responsibilities
### 1 swapi-scheduler-worker
Runs 
```sh
php artisan schedule:work
```
automatically to make sure all scheduled routines will called;
### 2 swapi-queue-worker
Runs 
```sh
php artisan queue:work
```
automatically to make sure all queue jobs will be executed. The queue is registered on Redis.
### 3 swapi-redis
Container that runs Redis cache, used to cache requests response for better perfomance and the job queue
### 4 swapi-frontend
Responsible for serve the react app
### 5 swapi-php-fpm
Runs php-fpm wich is responsible for php process
### 6 swapi-nginx
Web server with good performance, used with php-fpm to serve laravel app
### 7 swapi-postgres
Relational database (could be mysql) used to store the application data and framework config tables