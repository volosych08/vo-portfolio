<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Database;

$sqlitePath = getenv('SQLITE_SOURCE_PATH') ?: (getenv('SQLITE_PATH') ?: __DIR__ . '/../database.sqlite');

if (!file_exists($sqlitePath)) {
    fwrite(STDERR, "SQLite database not found at: {$sqlitePath}\n");
    exit(1);
}

$sqlite = new Database('sqlite:' . $sqlitePath);
$mysql = new Database(
    'mysql:host=' . getenv('DB_HOST') .
    ';port=' . (getenv('DB_PORT') ?: '3306') .
    ';dbname=' . getenv('DB_DATABASE') .
    ';charset=utf8mb4',
    getenv('DB_USERNAME') ?: null,
    getenv('DB_PASSWORD') ?: null
);

$tables = [
    'users' => ['id', 'username', 'password', 'name', 'role'],
    'blog_posts' => [
        'id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'card_image',
        'hero_image',
        'published_at',
        'created_at',
        'updated_at',
    ],
    'profile_sections' => ['id', 'section_key', 'title', 'content', 'updated_at'],
    'study_assessments' => [
        'id',
        'quartile',
        'course_name',
        'course_code',
        'exam_name',
        'exam_date',
        'earnable_credits',
        'grade',
        'sort_order',
        'created_at',
        'updated_at',
    ],
    'projects' => [
        'id',
        'title',
        'description',
        'long_description',
        'tech_json',
        'status',
        'completed_at',
        'category',
        'image',
        'url',
        'sort_order',
        'created_at',
        'updated_at',
    ],
];

$mysql->exec('SET FOREIGN_KEY_CHECKS = 0');

foreach (array_reverse(array_keys($tables)) as $table) {
    $mysql->exec('DELETE FROM ' . $table);
}

foreach ($tables as $table => $columns) {
    $columnList = implode(', ', $columns);
    $placeholderList = ':' . implode(', :', $columns);
    $rows = $sqlite->run('SELECT ' . $columnList . ' FROM ' . $table . ' ORDER BY id')->fetchAll();

    foreach ($rows as $row) {
        $params = [];

        foreach ($columns as $column) {
            $params[$column] = $row->{$column};
        }

        $mysql->run(
            'INSERT INTO ' . $table . ' (' . $columnList . ') VALUES (' . $placeholderList . ')',
            $params
        );
    }

    echo 'Copied ' . count($rows) . ' rows into ' . $table . ".\n";
}

$mysql->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "SQLite data copied to MySQL.\n";
