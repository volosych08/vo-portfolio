<?php

namespace App;

use App\Controllers\DashboardController;
use App\Controllers\FaqController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use Exception;
use Framework\Database;
use Framework\ResponseFactory;
use Framework\ServiceContainer;
use Framework\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(ServiceContainer $container): void
    {
        $responseFactory = $container->get(ResponseFactory::class);

        $database = $container->get(Database::class);

        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);

        $profileController = new ProfileController($responseFactory);
        $container->set(ProfileController::class, $profileController);

        $dashboardController = new DashboardController($responseFactory);
        $container->set(DashboardController::class, $dashboardController);

        $faqController = new FaqController($responseFactory);
        $container->set(FaqController::class, $faqController);
    }
}
