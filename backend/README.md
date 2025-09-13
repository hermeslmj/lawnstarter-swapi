1.Start the docker compose services
     docker compose -f compose.dev.yaml up -d
2. Install Laravel Dependencies (don't copy and paste all commands.)
    docker compose -f compose.dev.yaml exec workspace bash
    composer install
    php artisan key:generate
3. Run migration 
    docker compose -f compose.dev.yaml exec workspace php artisan migrate