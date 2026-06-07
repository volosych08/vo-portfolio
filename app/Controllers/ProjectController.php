<?php

namespace App\Controllers;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\ImageUploadService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProjectController
{
    private ResponseFactory $responseFactory;

    private ProjectRepositoryInterface $projectRepository;

    private ImageUploadService $uploadImageService;

    public function __construct(
        ResponseFactory $responseFactory,
        ProjectRepositoryInterface $projectRepository,
        ImageUploadService $uploadImageService
    ) {
        $this->responseFactory = $responseFactory;
        $this->projectRepository = $projectRepository;
        $this->uploadImageService = $uploadImageService;
    }

    public function index(): Response
    {
        return $this->responseFactory->view('projects/index.html.twig');
    }

    public function create(): Response
    {
        return $this->responseFactory->view('projects/create.html.twig', [
            'errors' => [],
        ]);
    }

    /**
     * @throws \JsonException
     */
    public function store(Request $request): Response
    {
        $errors = $this->validate($request);

        $values = [
            'title' => trim((string) ($request->get('title') ?? '')),
            'description' => trim((string) ($request->get('description') ?? '')),
            'long_description' => trim((string) ($request->get('long_description') ?? '')),
            'tech' => trim((string) ($request->get('tech') ?? '')),
            'status' => trim((string) ($request->get('status') ?? '')),
            'completed_at' => trim((string) ($request->get('completed_at') ?? '')),
            'category' => trim((string) ($request->get('category') ?? '')),
            'url' => trim((string) ($request->get('url') ?? '')),
            'sort_order' => trim((string) ($request->get('sort_order') ?? '')),
        ];

        if (!empty($errors)) {
            return $this->responseFactory->view('projects/create.html.twig', [
                'errors' => $errors,
                'values' => $values,
            ]);
        }

        try {
            $imagePath = $this->uploadImageService->uploadImage(
                'image_upload',
                'projects'
            );
        } catch (\RuntimeException $exception) {
            return $this->responseFactory->view('projects/create.html.twig', [
                'errors' => [$exception->getMessage()],
                'values' => $values,
            ]);
        }

        if ($imagePath === null || $imagePath === '') {
            return $this->responseFactory->view('projects/create.html.twig', [
                'errors' => ['Project image is required.'],
                'values' => $values,
            ]);
        }

        $tech = array_map(
            'trim',
            explode(',', $values['tech'])
        );

        $tech = array_values(array_filter(
            $tech,
            fn (string $technology): bool => $technology !== ''
        ));

        $now = date('Y-m-d H:i:s');

        $project = new Project();
        $project->title = $values['title'];
        $project->description = $values['description'];
        $project->longDescription = $values['long_description'];
        $project->tech = $tech;
        $project->status = $values['status'];
        $project->completedAt = $values['completed_at'];
        $project->category = $values['category'];
        $project->image = $imagePath;
        $project->url = $values['url'];
        $project->sortOrder = (int) $values['sort_order'];
        $project->createdAt = $now;
        $project->updatedAt = $now;

        $this->projectRepository->insert($project);

        return $this->responseFactory->redirect('/projects');
    }

    /**
     * @return string[]
     */
    private function validate(Request $request): array
    {
        $errors = [];

        $title = trim((string) ($request->get('title') ?? ''));
        $description = trim((string) ($request->get('description') ?? ''));
        $longDescription = trim((string) ($request->get('long_description') ?? ''));
        $tech = trim((string) ($request->get('tech') ?? ''));
        $status = trim((string) ($request->get('status') ?? ''));
        $completedAt = trim((string) ($request->get('completed_at') ?? ''));
        $category = trim((string) ($request->get('category') ?? ''));
        $url = trim((string) ($request->get('url') ?? ''));
        $sortOrder = trim((string) ($request->get('sort_order') ?? ''));

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        if ($longDescription === '') {
            $errors[] = 'Long description is required.';
        }

        if ($tech === '') {
            $errors[] = 'At least one technology is required.';
        }

        if (!in_array($status, ['Completed', 'In Progress', 'Planned'], true)) {
            $errors[] = 'Invalid project status selected.';
        }

        if ($completedAt === '') {
            $errors[] = 'Completion date is required.';
        } elseif (strtotime($completedAt) === false) {
            $errors[] = 'Completion date is invalid.';
        }

        if ($category === '') {
            $errors[] = 'Category is required.';
        }

        if ($url === '') {
            $errors[] = 'Project URL is required.';
        }

        if ($sortOrder === '') {
            $errors[] = 'Sort order is required.';
        } elseif (!ctype_digit($sortOrder) || (int) $sortOrder < 1) {
            $errors[] = 'Sort order must be a positive whole number.';
        }

        return $errors;
    }
}
