1.Start the docker compose services
     docker compose -f compose.dev.yaml up -d
2. Install Laravel Dependencies
    docker compose -f compose.dev.yaml exec workspace bash
    composer install
    npm install
    npm run dev
3. Run migration 
    docker compose -f compose.dev.yaml exec workspace php artisan migrate