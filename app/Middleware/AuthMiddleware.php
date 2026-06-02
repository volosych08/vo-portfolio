<?php

namespace App\Middleware;

use App\Services\AuthService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class AuthMiddleware
{
    private AuthService $authService;

    private ResponseFactory $responseFactory;

    public function __construct(AuthService $authService, ResponseFactory $responseFactory)
    {
        $this->authService = $authService;
        $this->responseFactory = $responseFactory;
    }

    public function handle(Request $request, callable $next): Response
    {
        $userId = $request->session->get('user_id');
        if ($userId) {
            $user = $this->authService->validateUser((int)$userId);
            if ($user) {
                $request->setAttribute('user', $user);
                $this->responseFactory->globalContext['user'] = $user;
            }
        }

        return $next($request);
    }

    public function requireAuth(Request $request, callable $next): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return $this->responseFactory->notAuthorized();
        }

        return $next($request);
    }

    public function requireAdmin(Request $request, callable $next): Response
    {
        $user = $request->getAttribute('user');
        if (!$user || $user->role !== 'admin') {
            return $this->responseFactory->notAuthorized();
        }

        return $next($request);
    }
}
