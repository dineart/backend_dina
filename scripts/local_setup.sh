#!/bin/bash

echo "Setup laravel docker local environment"

if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker first."
    exit 1
fi

if [ ! -f .env ]; then
    echo "Copying environment file..."
    cp .env.example .env
    sed -i 's/DB_DATABASE=/DB_DATABASE=app/' .env
    sed -i 's/DB_USERNAME=/DB_USERNAME=laravel/' .env
    sed -i 's/DB_PASSWORD=/DB_PASSWORD=secret/' .env
else
    echo ".env file already exists. Skipping copy."
fi

echo "Building and starting Docker containers..."
docker compose -f docker-compose.local.yml up -d --build

echo -n "Waiting for services to initialize."
until docker compose -f docker-compose.local.yml exec -T mysql mysqladmin ping -h"127.0.0.1" --silent; do
    echo -n "."
    sleep 2
done
echo -e "\nDatabase is ready to accept connections!"

if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    docker compose -f docker-compose.local.yml exec php php artisan key:generate
fi

echo "Running database migrations..."
docker compose -f docker-compose.local.yml exec php php artisan migrate --force

# Clear and cache configs
echo "Optimizing Laravel..."
docker compose -f docker-compose.local.yml exec php php artisan config:clear
docker compose -f docker-compose.local.yml exec php php artisan cache:clear
docker compose -f docker-compose.local.yml exec php php artisan route:clear
docker compose -f docker-compose.local.yml exec php php artisan view:clear

echo "Setup complete!"
echo "Your application is running at: http://localhost:8871"
echo "Database is available at: localhost:33061"