<?php

namespace App\Controllers;

use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProjectApiController extends ApiBaseController
{
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ResponseFactory $responseFactory,
        ProjectRepositoryInterface $projectRepository
    ) {
        parent::__construct($responseFactory);

        $this->projectRepository = $projectRepository;
    }

    public function projects(Request $request): Response
    {
        $projects = $this->projectRepository->all();

        $overviewData = array_map(
            fn (Project $project): array => $this->toOverviewArray($project),
            $projects
        );

        return $this->apiJson(
            'projects',
            $overviewData,
            $request->path,
            total: count($overviewData)
        );
    }

    public function project(Request $request): Response
    {
        $id = (int) ($request->routeParameters['id'] ?? 0);

        $project = $this->projectRepository->findById($id);

        if ($project === null) {
            return $this->apiError(
                'projects',
                'Project not found',
                $request->path,
                404,
                '/api/projects'
            );
        }

        return $this->apiJson(
            'projects',
            $this->toDetailArray($project),
            $request->path,
            '/api/projects'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function toOverviewArray(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status,
            'date' => $this->formatDate($project->completedAt),
            'image' => $project->image,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toDetailArray(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'longDescription' => $project->longDescription,
            'tech' => $project->tech,
            'status' => $project->status,
            'date' => $this->formatDate($project->completedAt),
            'category' => $project->category,
            'image' => $project->image,
            'url' => $project->url,
        ];
    }

    private function formatDate(string $date): string
    {
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return $date;
        }

        return date('d.m.Y', $timestamp);
    }
}
