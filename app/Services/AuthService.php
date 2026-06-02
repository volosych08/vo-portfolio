<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Framework\Session;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(User $user, string $password): User
    {
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userRepository->insert($user);
        return $user;
    }

    public function loginWithCredentials(string $username, string $password, Session $session): User|false
    {
        $user = $this->userRepository->findByUsername($username);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $session->destroy();
        $session->set('user_id', $user->id);
        return $user;
    }

    public function forceLogin(User $user, Session $session): void
    {
        $session->destroy();
        $session->set('user_id', $user->id);
    }

    public function validateUser(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function logout(Session $session): void
    {
        $session->destroy();
    }
}
