# ITDP Portfolio Website

## Run Locally with Docker

This project can be run locally using Docker in three different ways:

1. Manual Docker commands with SQLite
2. Docker Compose with SQLite
3. Docker Compose with MySQL in a separate database container

---

## Step 1: Manual Docker Run with SQLite

Build the Docker image:

```bash
docker build -t itdp-volosych08-app .
```

Create Docker volumes:

```bash
docker volume create itdp_sqlite_data
docker volume create itdp_uploads_data
```

Run the container:

```bash
docker run -d \
  --name itdp-volosych08-app \
  -p 8888:8888 \
  -e DB_CONNECTION=sqlite \
  -e SQLITE_PATH=/var/www/html/storage/database.sqlite \
  -v itdp_sqlite_data:/var/www/html/storage \
  -v itdp_uploads_data:/var/www/html/public/uploads \
  itdp-volosych08-app
```

Run the database migration:

```bash
docker exec itdp-volosych08-app php database/migrate.php
```

Open the website:

```text
http://localhost:8888
```

Stop and remove the container:

```bash
docker stop itdp-volosych08-app
docker rm itdp-volosych08-app
```

---

## Step 2: Docker Compose with SQLite

Start the application:

```bash
docker compose up --build -d
```

Run the database migration:

```bash
docker compose exec app php database/migrate.php
```

Open the website:

```text
http://localhost:8888
```

Stop the application:

```bash
docker compose down
```

Do not use `docker compose down -v` unless you want to delete the SQLite database and uploaded files.

---

## Step 3: Docker Compose with MySQL

Start the application with a separate MySQL database container:

```bash
docker compose -f docker-compose.mysql.yml up --build -d
```

Run the MySQL database migration. This creates the schema and inserts the saved MySQL data:

```bash
docker compose -f docker-compose.mysql.yml exec app php database/migrate.php
```

Open the website:

```text
http://localhost:8888
```

Stop the MySQL setup:

```bash
docker compose -f docker-compose.mysql.yml down
```

Do not use `docker compose -f docker-compose.mysql.yml down -v` unless you want to delete the MySQL database and uploaded files.

---

## Stop All Local Docker Containers

To check running containers:

```bash
docker ps
```

To stop all running containers:

```bash
docker stop $(docker ps -q)
```

---

## Run Tests

Run PHPUnit tests:

```bash
php maestro phpunit
```

Run XDEBUG for coverage:

```bash
php maestro xdebug
```

---

## Quality Checks

Run PHPStan:

```bash
php maestro phpstan
```

Run PHPCS:

```bash
php maestro phpcs
```

Run Deptrac:

```bash
php maestro deptrac
```
