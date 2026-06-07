Run locally:
docker compose up --build -d
docker compose exec app php database/migrate.php

close:
docker compose down
