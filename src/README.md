# ClarityQuest

## Requirements

- PHP version 8.3 or higher
- Composer version 2.2 or higher

The required software above can be installed on Linux with:
```
sudo apt update
sudo apt install git composer php8.3 php8.3-mbstring php8.3-xml php8.3-pgsql php8.3-curl
```

- [Docker](https://www.docker.com/products/docker-desktop/)

## Setting up a development environmnet

- Update PHP dependencies.
```
composer update
```

- Start database container
```
docker compose up -d
```

- Seed the database
```
php artisan db:seed
```

- Start the development server (run both commands in different terminal windows)
```
php artisan serve
```
```
npm run dev
```

- Access the website!
```
http://127.0.0.1:8000/
```

## Deploying the project on FEUP's servers

> TODO

## References

- [LBAW Laravel Template](https://gitlab.up.pt/lbaw/template-laravel)