<?php

namespace App;

use App\Controllers\DashboardController;
use App\Controllers\FaqController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use Framework\RouteProviderInterface;
use Framework\Router;
use Framework\ServiceContainer;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @throws \Exception
     */
    public function register(Router $router, ServiceContainer $container): void
    {
        $authMiddleware = $container->get(AuthMiddleware::class);
        $homeController = $container->get(HomeController::class);
        $router->addRoute('GET', '/', [$homeController, "index"]);

        $profileController = $container->get(ProfileController::class);
        $router->addRoute('GET', '/profile', [$profileController, "index"]);

        $dashboardController = $container->get(DashboardController::class);
        $router->addRoute('GET', '/dashboard', [$dashboardController, "index"]);

        $faqController = $container->get(FaqController::class);
        $router->addRoute('GET', '/faq', [$faqController, "index"]);

        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/register', [$userController, 'registerForm']);
        $router->addRoute('POST', '/register', [$userController, 'register']);
        $router->addRoute('GET', '/login', [$userController, 'loginForm']);
        $router->addRoute('POST', '/login', [$userController, 'login']);
        $router->addRoute('GET', '/logout', [$userController, 'logout']);

        $router->addMiddleware([$authMiddleware, 'handle']);

        $csrfMiddleware = $container->get(CsrfMiddleware::class);
        $router->addMiddleware([$csrfMiddleware, 'handle']);
    }
}
