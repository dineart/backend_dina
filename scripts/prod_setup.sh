#!/bin/bash

echo "Setup laravel docker production environment"

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

    set -o allexport
    source .env
    set +o allexport

    read -p "Input your email for issue SSL certificate:  " email

    export EMAIL=$email
else
    echo ".env file already exists. Skipping copy."
fi

echo "Building Docker containers..."
docker compose -f docker-compose.prod.yml build --no-cache

echo "Issue Certificate for SSL..."
docker compose run --rm certbot certonly \ 
    --webroot -w /var/www/certbot \ 
    -d api-pegawai-4a.akufarish.my.id \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email

echo "Starting Docker containers..."
docker compose -f docker-compose.prod.yml up -d

echo -n "Waiting for services to initialize."
until docker compose -f docker-compose.prod.yml exec -T mysql mysqladmin ping -h"127.0.0.1" --silent; do
    echo -n "."
    sleep 2
done
echo -e "\nDatabase is ready to accept connections!"

if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    docker compose -f docker-compose.prod.yml exec php php artisan key:generate
fi


echo "Running database migrations..."
docker compose -f docker-compose.local.yml exec php php artisan migrate --force

# Clear and cache configs
echo "Optimizing Laravel..."
docker compose -f docker-compose.prod.yml exec php php artisan config:clear
docker compose -f docker-compose.prod.yml exec php php artisan cache:clear
docker compose -f docker-compose.prod.yml exec php php artisan route:clear
docker compose -f docker-compose.prod.yml exec php php artisan view:clear

echo "Setup complete!"
echo "Your application is running at: https://api-pegawai-4a.akufarish.my.id:1234"
echo "Database is available at: localhost:33060"