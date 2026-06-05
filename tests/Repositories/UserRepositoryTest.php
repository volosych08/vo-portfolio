<?php

namespace Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        $database = $this->createTestDatabase();

        $database->run("DROP TABLE IF EXISTS users");

        $database->run("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                name TEXT NOT NULL,
                role TEXT NOT NULL
            )
        ");

        $database->run("
            INSERT INTO users (
                username,
                password,
                name,
                role
            ) VALUES
            (
                'admin',
                'hashed-password',
                'Admin User',
                'admin'
            ),
            (
                'john',
                'user-password',
                'John Doe',
                'user'
            )
        ");

        $this->repository = new UserRepository($database);
    }

    public function testAllReturnsAllUsers(): void
    {
        $users = $this->repository->all();

        $this->assertCount(2, $users);
        $this->assertSame('admin', $users[0]->username);
        $this->assertSame('john', $users[1]->username);
    }

    public function testFindByIdReturnsUser(): void
    {
        $user = $this->repository->findById(1);

        $this->assertNotNull($user);
        $this->assertSame('admin', $user->username);
        $this->assertSame('Admin User', $user->name);
        $this->assertSame('admin', $user->role);
    }

    public function testFindByIdReturnsNullWhenUserDoesNotExist(): void
    {
        $user = $this->repository->findById(999);

        $this->assertNull($user);
    }

    public function testFindByUsernameReturnsUser(): void
    {
        $user = $this->repository->findByUsername('john');

        $this->assertNotNull($user);
        $this->assertSame('John Doe', $user->name);
        $this->assertSame('user', $user->role);
    }

    public function testFindByUsernameReturnsNullWhenUserDoesNotExist(): void
    {
        $user = $this->repository->findByUsername('missing');

        $this->assertNull($user);
    }

    public function testInsertCreatesNewUser(): void
    {
        $user = new User();
        $user->username = 'newuser';
        $user->password = 'new-password';
        $user->name = 'New User';
        $user->role = 'user';

        $insertedUser = $this->repository->insert($user);

        $this->assertGreaterThan(0, $insertedUser->id);

        $foundUser = $this->repository->findByUsername('newuser');

        $this->assertNotNull($foundUser);
        $this->assertSame('New User', $foundUser->name);
        $this->assertSame('user', $foundUser->role);
    }

    public function testUpdateChangesExistingUser(): void
    {
        $user = $this->repository->findByUsername('john');

        $this->assertNotNull($user);

        $user->username = 'updated-john';
        $user->password = 'updated-password';
        $user->name = 'Updated John';
        $user->role = 'admin';

        $this->repository->update($user);

        $updatedUser = $this->repository->findByUsername('updated-john');

        $this->assertNotNull($updatedUser);
        $this->assertSame('Updated John', $updatedUser->name);
        $this->assertSame('admin', $updatedUser->role);
    }

    public function testDeleteRemovesUser(): void
    {
        $user = $this->repository->findByUsername('john');

        $this->assertNotNull($user);

        $deleted = $this->repository->delete($user);

        $this->assertTrue($deleted);
        $this->assertNull($this->repository->findByUsername('john'));
    }

    private function createTestDatabase(): Database
    {
        return new Database('sqlite::memory:');
    }
}
