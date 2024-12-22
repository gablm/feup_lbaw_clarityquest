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

- Set up the development environment

- Login to GitLab's Container Registry (using FEUP VPN/network):
```bash
docker login gitlab.up.pt:5050
```

- Build and upload the project's image
```bash
./upload_image.sh
```

### Testing the image

- After publishing, the image can be tested locally using:
```bash
docker run -d --name lbaw2425 -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2425/lbaw24125
```
#### 2.2. Running the project

The project can be run inside FEUP's network or using VPN by downloading an pre-built image from GitLab's registry. Note that `docker` is required to run the project.

Running the command below will download the latest version of ClarityQuest's image and start it on the background.

```
docker run -d --name lbaw2425 -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2425/lbaw24125
```

After starting the image, the website can be accessed via [http://localhost:8001](http://localhost:8001).

### Access Credentials

#### 3.1. Administration Credentials

* Administration URL: [https://localhost:8001/admin](https://localhost:8001/admin)

| Email | Password | Username |
|-------|----------|----------|
| admin@clarityquest.com | admin-clarityquest | admin |

#### 3.2. User Credentials

| Type | Email | Password | Username |
|------|-------|----------|----------|
| Basic Account | user@clarityquest.com | user-clarityquest | user |
| Moderator | mod@clarityquest.com | mod-clarityquest | mod |

## References

- [LBAW Laravel Template](https://gitlab.up.pt/lbaw/template-laravel)