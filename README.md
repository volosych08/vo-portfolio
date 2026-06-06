# Portfolio website

## DevOps / Docker

This project supports two Docker database modes:

* SQLite: simple local mode, used for assignment steps 1 and 2.
* MySQL: separate database container, used for assignment step 3.

### Step 1: Manual Docker build and run with SQLite

Use this when you want to build and run the app manually with Docker commands.

Commands:

```bash
docker build -t itdp-volosych08-app .
docker volume create itdp_sqlite_data
docker volume create itdp_uploads_data
docker run -d \
  --name itdp-volosych08-app \
  -p 8888:8888 \
  -e DB_CONNECTION=sqlite \
  -e SQLITE_PATH=/var/www/html/storage/database.sqlite \
  -v itdp_sqlite_data:/var/www/html/storage \
  -v itdp_uploads_data:/var/www/html/public/uploads \
  itdp-volosych08-app
docker exec itdp-volosych08-app php database/migrate.php
```

Open:

```text
http://localhost:8888
```

Stop/remove:

```bash
docker stop itdp-volosych08-app
docker rm itdp-volosych08-app
```

The SQLite database is stored in the `itdp_sqlite_data` Docker volume.
Uploaded images are stored in the `itdp_uploads_data` Docker volume.

### Step 2: Docker Compose with SQLite

Use this for the simplest SQLite setup. It starts the PHP app container with one Compose command.

Commands:

```bash
docker compose up --build -d
docker compose exec app php database/migrate.php
```

Open:

```text
http://localhost:8888
```

Stop:

```bash
docker compose down
```

The SQLite database is stored in the `itdp-volosych08_sqlite_data` Docker volume.
Uploaded images are stored in the `itdp-volosych08_uploads_data` Docker volume.

### Step 3: Docker Compose with MySQL and phpMyAdmin

Use this when you want the app to use MySQL instead of SQLite. This starts:

* PHP app: `http://localhost:8888`
* MySQL database container
* phpMyAdmin: `http://localhost:8081`

Commands:

```bash
docker compose -f docker-compose.mysql.yml up --build -d
docker compose -f docker-compose.mysql.yml exec app php database/migrate.php
```

If you want to copy the current SQLite data into MySQL, run:

```bash
docker compose -f docker-compose.mysql.yml exec app php database/copy_sqlite_to_mysql.php
```

Open app:

```text
http://localhost:8888
```

Open phpMyAdmin:

```text
http://localhost:8081
```

phpMyAdmin login:

* Server: `db`
* User: `root`
* Password: `root_password`

Stop:

```bash
docker compose -f docker-compose.mysql.yml down
```

The MySQL database is stored in the `itdp-volosych08_mysql_data` Docker volume.
Uploaded images are stored in the `itdp-volosych08_uploads_data` Docker volume.

Reset MySQL database completely:

```bash
docker compose -f docker-compose.mysql.yml down -v
```

Warning: `down -v` deletes Docker volumes. This removes the MySQL database and uploaded images stored in the Compose volumes.

## Production Deployment

The production setup uses Caddy for HTTPS on `volosych.dev`, the PHP app container, and a MySQL database container.

Before starting the app, point the DNS A record for `volosych.dev` to the Droplet public IP address. Ports `80` and `443` must be open on the Droplet.

Start production:

```bash
docker compose -f docker-compose.prod.yml up --build -d
docker compose -f docker-compose.prod.yml exec app php database/migrate.php
```

Open:

```text
https://volosych.dev
```

Stop production:

```bash
docker compose -f docker-compose.prod.yml down
```

Production MySQL is not exposed publicly. It is only available to the app container over the Docker network.
