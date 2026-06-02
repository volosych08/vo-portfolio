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

    private AuthService $authService;

    public function __construct(
        ResponseFactory $responseFactory,
        AuthService $authService
    ) {
        $this->responseFactory = $responseFactory;
        $this->authService = $authService;
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

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->session);
        return $this->responseFactory->redirect('/');
    }
}
