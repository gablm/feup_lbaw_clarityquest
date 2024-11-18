#!/bin/bash

# Stop execution if a step fails
set -e

IMAGE_NAME=gitlab.up.pt:5050/lbaw/lbaw24125/lbaw24125

# Ensure that dependencies are available
composer install
npm install
npm run build
php artisan config:clear
php artisan clear-compiled
php artisan optimize

# docker buildx build --push --platform linux/amd64 -t $IMAGE_NAME .
docker build -t $IMAGE_NAME .
docker push $IMAGE_NAME
