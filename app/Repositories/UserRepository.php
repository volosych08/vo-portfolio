<?php

namespace App\Repositories;

use Framework\Database;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return User[]
     */
    public function all(): array
    {
        $stmt = $this->database->run("SELECT * FROM users")->fetchAll();
        $users = [];
        foreach ($stmt as $row) {
            $user = $this->fromDbRow($row);
            $users[] = $user;
        }
        return $users;
    }

    public function insert(User $user): User
    {
        $stmt = $this->database->run("INSERT INTO users (username, password, name, role) VALUES (:username, :password, :name, :role)", [
            "username" => $user->username,
            "password" => $user->password,
            "name" => $user->name,
            "role" => $user->role
        ]);
        $user->id = $this->database->getLastID();
        return $user;
    }

    public function update(User $user): User
    {
        $stmt = $this->database->run("UPDATE users SET username = :username, password = :password, name = :name, role = :role WHERE id = :id", [
            "username" => $user->username,
            "password" => $user->password,
            "name" => $user->name,
            "role" => $user->role,
            "id" => $user->id
        ]);
        return $user;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->database->run("SELECT * FROM users WHERE id = :id", ["id" => $id])->fetch();
        if (!$stmt) {
            return null;
        }
        $user = $this->fromDbRow($stmt);

        return $user;
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->database->run("SELECT * FROM users WHERE username = :username", ["username" => $username])->fetch();
        if (!$stmt) {
            return null;
        }
        $user = $this->fromDbRow($stmt);

        return $user;
    }

    public function delete(User $user): bool
    {
        $stmt = $this->database->run("DELETE FROM users WHERE id = :id", ["id" => $user->id]);
        return $stmt->rowCount() > 0;
    }

    private function fromDbRow(mixed $row): User
    {
        $user = new User();
        $user->id = $row->id;
        $user->username = $row->username;
        $user->password = $row->password;
        $user->name = $row->name;
        $user->role = $row->role;
        return $user;
    }
}
