# ClarityQuest

## Requirements

- PHP version 8.3 or higher
- Composer version 2.2 or higher

The required software above can be installed on Linux with:
```bash
sudo apt update
sudo apt install git composer php8.3 php8.3-mbstring php8.3-xml php8.3-pgsql php8.3-curl
```

- [Docker](https://www.docker.com/products/docker-desktop/)

## Setting up a development environmnet

- Update PHP dependencies.
```bash
composer update
```

- Update JS dependencies.
```bash
npm install
```

- Start database container
```bash
docker compose up -d
```

- Seed the database
```bash
php artisan db:seed
```

- Start the development server (run both commands in different terminal windows)
```bash
php artisan serve
```
```bash
npm run dev
```

- Access the website!
```
http://127.0.0.1:8000/
```

## Deploying the project on FEUP's servers

- Set up the development evnironment

- Login to GitLab's Container Registry (using FEUP VPN/network):
```bash
docker login gitlab.up.pt:5050
```

- Build and upload the project's image
```bash
./upload_image.sh
```

### Testing your image

- After publishing, the image can be tested locally using:
```bash
docker run -d --name lbawYYXX -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2425/lbaw24125
```

## References

- [LBAW Laravel Template](https://gitlab.up.pt/lbaw/template-laravel)