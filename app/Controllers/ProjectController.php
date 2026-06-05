<?php

namespace App\Controllers;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProjectController
{
    private ResponseFactory $responseFactory;

    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ResponseFactory $responseFactory,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->responseFactory = $responseFactory;
        $this->projectRepository = $projectRepository;
    }

    public function index(): Response
    {
        return $this->responseFactory->view("projects/index.html.twig");
    }

    public function create(): Response
    {
        return $this->responseFactory->view('projects/create.html.twig');
    }

    /**
     * @throws \JsonException
     */
    public function store(Request $request): Response
    {
        $errors = $this->validate($request);

        $values = [
            'title' => trim($request->get('title') ?? ''),
            'description' => trim($request->get('description') ?? ''),
            'long_description' => trim($request->get('long_description') ?? ''),
            'tech' => trim($request->get('tech') ?? ''),
            'status' => trim($request->get('status') ?? ''),
            'completed_at' => trim($request->get('completed_at') ?? ''),
            'category' => trim($request->get('category') ?? ''),
            'image' => trim($request->get('image') ?? ''),
            'url' => trim($request->get('url') ?? ''),
            'sort_order' => trim($request->get('sort_order') ?? ''),
        ];

        if (!empty($errors)) {
            return $this->responseFactory->view('projects/create.html.twig', [
                'errors' => $errors,
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
        $project->image = $values['image'];
        $project->url = $values['url'];
        $project->sortOrder = (int) $values['sort_order'];
        $project->createdAt = $now;
        $project->updatedAt = $now;

        $this->projectRepository->insert($project);

        return $this->responseFactory->redirect('/projects-3d');
    }

    /**
     * @return string[]
     */
    private function validate(Request $request): array
    {
        $errors = [];

        $title = trim($request->get('title') ?? '');
        $description = trim($request->get('description') ?? '');
        $longDescription = trim($request->get('long_description') ?? '');
        $tech = trim($request->get('tech') ?? '');
        $status = trim($request->get('status') ?? '');
        $completedAt = trim($request->get('completed_at') ?? '');
        $category = trim($request->get('category') ?? '');
        $image = trim($request->get('image') ?? '');
        $url = trim($request->get('url') ?? '');
        $sortOrder = trim($request->get('sort_order') ?? '');

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

        if ($image === '') {
            $errors[] = 'Image path is required.';
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
