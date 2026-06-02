<?php

namespace App;

use App\Controllers\BlogController;
use App\Controllers\DashboardController;
use App\Controllers\FaqController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\ProjectController;
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

        $projectController = $container->get(ProjectController::class);
        $router->addRoute('GET', '/projects', [$projectController, "index"]);

        $profileController = $container->get(ProfileController::class);
        $router->addRoute('GET', '/profile', [$profileController, "index"]);
        $router->addRoute('GET', '/profile', [$profileController, 'index']);

        $router
            ->addRoute('GET', '/admin/profile/edit', [$profileController, 'edit'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('POST', '/admin/profile/edit', [$profileController, 'update'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $dashboardController = $container->get(DashboardController::class);
        $router->addRoute('GET', '/dashboard', [$dashboardController, "index"]);

        $faqController = $container->get(FaqController::class);
        $router->addRoute('GET', '/faq', [$faqController, "index"]);

        $router->addMiddleware([$authMiddleware, 'handle']);

        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/login', [$userController, 'loginForm']);
        $router->addRoute('POST', '/login', [$userController, 'login']);
        $router->addRoute('GET', '/logout', [$userController, 'logout']);

        $blogController = $container->get(BlogController::class);
        $router->addRoute('GET', '/blog', [$blogController, 'index']);
        $router->addRoute('GET', '/blog/(?<slug>[a-z0-9-]+)', [$blogController, 'show']);
        $router
            ->addRoute('GET', '/admin/blog/edit/(?<slug>[a-z0-9-]+)', [$blogController, 'edit'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('POST', '/admin/blog/edit/(?<slug>[a-z0-9-]+)', [$blogController, 'update'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('GET', '/admin/blog/create', [$blogController, 'create'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('POST', '/admin/blog/create', [$blogController, 'store'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('POST', '/admin/blog/delete/(?<slug>[a-z0-9-]+)', [$blogController, 'delete'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $dashboardController = $container->get(DashboardController::class);

        $router->addRoute('GET', '/dashboard', [$dashboardController, 'index']);

        $router
            ->addRoute('GET', '/admin/dashboard/grades', [$dashboardController, 'edit'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $router
            ->addRoute('POST', '/admin/dashboard/grades', [$dashboardController, 'update'])
            ->addMiddleware([$authMiddleware, 'requireAdmin']);

        $csrfMiddleware = $container->get(CsrfMiddleware::class);
        $router->addMiddleware([$csrfMiddleware, 'handle']);
    }
}
