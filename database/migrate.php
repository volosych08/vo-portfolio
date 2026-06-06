<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Database;

$connection = getenv('DB_CONNECTION') ?: 'sqlite';

if ($connection === 'mysql') {
    $database = new Database(
        'mysql:host=' . getenv('DB_HOST') .
        ';port=' . (getenv('DB_PORT') ?: '3306') .
        ';dbname=' . getenv('DB_DATABASE') .
        ';charset=utf8mb4',
        getenv('DB_USERNAME') ?: null,
        getenv('DB_PASSWORD') ?: null
    );

    $database->migrate(__DIR__ . '/migrations/mysql');
} else {
    $database = new Database(getenv('SQLITE_PATH') ?: __DIR__ . '/../database.sqlite');
    $database->migrate(__DIR__ . '/migrations/sqlite');
    $database->migrate(__DIR__);
}

echo "Migration completed.\n";
