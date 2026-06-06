<?php

namespace Framework;

use PDO;
use PDOStatement;

class Database
{
    private PDO $connection;

    private string $dsn;

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null
    ) {
        $this->dsn = $this->normalizeDsn($dsn);

        $this->connection = new PDO(
            $this->dsn,
            $username,
            $password
        );

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        if (str_starts_with($this->dsn, 'sqlite:')) {
            $this->connection->exec('PRAGMA foreign_keys = ON;');
        }
    }

    private function normalizeDsn(string $dsn): string
    {
        if (
            str_starts_with($dsn, 'sqlite:')
            || str_starts_with($dsn, 'mysql:')
            || str_starts_with($dsn, 'pgsql:')
        ) {
            return $dsn;
        }

        return 'sqlite:' . $dsn;
    }

    public function query(string $query): PDOStatement | false
    {
        return $this->connection->query($query);
    }

    /**
     * @param mixed[]|null $params
     */
    public function run(string $sql, array|null $params = null): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }

    public function exec(string $sql): false|int
    {
        return $this->connection->exec($sql);
    }

    public function getLastID(string|null $field = null): int
    {
        return (int) $this->connection->lastInsertId($field);
    }

    public function migrate(string $migrationsDirectory): void
    {
        $files = scandir($migrationsDirectory);

        if ($files === false) {
            die('Could not read database migration files');
        }

        sort($files);
        $this->ensureMigrationsTable();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (!str_ends_with($file, '.sql')) {
                continue;
            }

            if ($this->hasMigrationRun($file)) {
                continue;
            }

            echo 'Migrating: ' . $file . "\n";

            $path = rtrim($migrationsDirectory, '/') . '/' . $file;
            $contents = file_get_contents($path);

            if ($contents !== false) {
                $this->connection->exec($contents);
                $this->recordMigration($file);
            }
        }
    }

    private function ensureMigrationsTable(): void
    {
        $this->connection->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                filename VARCHAR(255) PRIMARY KEY,
                executed_at VARCHAR(255) NOT NULL
            )'
        );
    }

    private function hasMigrationRun(string $file): bool
    {
        $stmt = $this->connection->prepare('SELECT filename FROM schema_migrations WHERE filename = :filename');
        $stmt->execute(['filename' => $file]);

        return $stmt->fetch() !== false;
    }

    private function recordMigration(string $file): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO schema_migrations (filename, executed_at) VALUES (:filename, :executedAt)'
        );

        $stmt->execute([
            'filename' => $file,
            'executedAt' => date('Y-m-d H:i:s'),
        ]);
    }
}
