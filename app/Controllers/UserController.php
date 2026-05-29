<?php

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class UserController
{
    private ResponseFactory $responseFactory;

    private UserRepositoryInterface $userRepository;

    private AuthService $authService;

    public function __construct(
        ResponseFactory $responseFactory,
        UserRepositoryInterface $userRepository,
        AuthService $authService
    ) {
        $this->responseFactory = $responseFactory;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function registerForm(Request $request): Response
    {
        return $this->responseFactory->view('users/register.html.twig');
    }

    public function loginForm(Request $request): Response
    {
        return $this->responseFactory->view('users/login.html.twig');
    }

    public function login(Request $request): Response
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $errors = [];
        if ($username === null || trim($username) === '') {
            $errors['username'] = "Username is required.";
        }

        if ($password === null || trim($password) === '') {
            $errors['password'] = "Password is required.";
        }

        assert(is_string($username));
        assert(is_string($password));

        if (!$this->authService->loginWithCredentials($username, $password, $request->session)) {
            $errors['general'] = "Invalid username or password.";
        }

        if (!empty($errors)) {
            return $this->responseFactory->view('users/login.html.twig', [
                'errors' => $errors,
                'username' => $username
            ]);
        }

        return $this->responseFactory->redirect('/');
    }

    public function register(Request $request): Response
    {
        $name = $request->get('name') ?? '';
        $username = $request->get('username');
        $password = $request->get('password');
        $confirmPassword = $request->get('confirm_password');

        $errors = [];
        if ($username === null || trim($username) === '') {
            $errors['username'] = "Username is required.";
        } elseif ($this->userRepository->findByUsername($username) !== null) {
            $errors['username'] = "Username is already taken.";
        }

        if ($password === null || trim($password) === '') {
            $errors['password'] = "Password is required.";
        } elseif ($confirmPassword === null || trim($confirmPassword) === '') {
            $errors['confirm_password'] = "Confirm password is required.";
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = "Passwords do not match.";
        }
        if (!empty($errors)) {
            return $this->responseFactory->view('users/register.html.twig', [
                'errors' => $errors,
                'username' => $username
            ]);
        }

        assert(is_string($username));
        assert(is_string($password));

        $user = new User();
        $user->name = $name;
        $user->username = $username;
        $user = $this->authService->register($user, $password);

        // Log the user in after successful registration
        $this->authService->forceLogin($user, $request->session);

        return $this->responseFactory->redirect('/');
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->session);
        return $this->responseFactory->redirect('/');
    }
}
