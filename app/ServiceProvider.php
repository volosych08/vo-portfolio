<?php

namespace App;

use App\Controllers\BlogController;
use App\Controllers\DashboardController;
use App\Controllers\FaqController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\ProjectApiController;
use App\Controllers\ProjectController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Repositories\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\Repositories\ProfileRepository;
use App\Repositories\ProfileRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\StudyAssessmentRepository;
use App\Repositories\StudyAssessmentRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\ImageUploadService;
use App\Services\StudyProgressService;
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

        $projectRepository = new ProjectRepository($database);
        $container->set(ProjectRepositoryInterface::class, $projectRepository);

        $uploadImageService = new ImageUploadService();
        $container->set(ImageUploadService::class, $uploadImageService);

        $projectApiController = new ProjectApiController($responseFactory, $projectRepository);
        $container->set(ProjectApiController::class, $projectApiController);

        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);


        $studyAssessmentRepository = new StudyAssessmentRepository($database);
        $container->set(StudyAssessmentRepositoryInterface::class, $studyAssessmentRepository);
        $studyProgressService = new StudyProgressService();
        $container->set(StudyProgressService::class, $studyProgressService);

        $dashboardController = new DashboardController(
            $responseFactory,
            $studyAssessmentRepository,
            $studyProgressService
        );

        $container->set(DashboardController::class, $dashboardController);

        $faqController = new FaqController($responseFactory);
        $container->set(FaqController::class, $faqController);

        $userRepository = new UserRepository($database);

        $authService = new AuthService($userRepository);
        $authMiddleware = new AuthMiddleware($authService, $responseFactory);
        $container->set(AuthMiddleware::class, $authMiddleware);

        $csrfService = new CsrfService($responseFactory);
        $csrfMiddleware = new CsrfMiddleware($csrfService);
        $container->set(CsrfMiddleware::class, $csrfMiddleware);

        $userController = new UserController($responseFactory, $authService);
        $container->set(UserController::class, $userController);

        $projectController = new ProjectController($responseFactory, $projectRepository);
        $container->set(ProjectController::class, $projectController);

        $blogRepository = new BlogRepository($database);
        $container->set(BlogRepositoryInterface::class, $blogRepository);

        $blogController = new BlogController($responseFactory, $blogRepository);
        $container->set(BlogController::class, $blogController);

        $profileRepository = new ProfileRepository($database);
        $container->set(ProfileRepositoryInterface::class, $profileRepository);

        $profileController = new ProfileController($responseFactory, $profileRepository);
        $container->set(ProfileController::class, $profileController);
    }
}
