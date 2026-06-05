<?php

namespace Controllers;

use App\Controllers\ProjectApiController;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Framework\Request;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class ProjectApiControllerTest extends TestCase
{
    public function testProjectsReturnsOverviewJson(): void
    {
        $repository = $this->createMock(ProjectRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn([
                $this->createProject(1, 'Portfolio Website', '2026-06-01'),
                $this->createProject(2, 'Blog System', '2026-06-02'),
            ]);

        $controller = new ProjectApiController(
            new ResponseFactory(false, 'app/views'),
            $repository
        );

        $request = new Request('GET', '/api/projects', [], []);

        $response = $controller->projects($request);

        $this->assertSame(200, $response->responseCode);
        $this->assertSame('Content-Type: application/json', $response->header);

        $json = json_decode($response->body, true);

        $this->assertSame('projects', $json['meta']['resource']);
        $this->assertSame(2, $json['meta']['total']);
        $this->assertSame('/api/projects', $json['links']['self']);

        $this->assertCount(2, $json['data']);
        $this->assertSame(1, $json['data'][0]['id']);
        $this->assertSame('Portfolio Website', $json['data'][0]['title']);
        $this->assertSame('01.06.2026', $json['data'][0]['date']);

        $this->assertArrayNotHasKey('longDescription', $json['data'][0]);
        $this->assertArrayNotHasKey('tech', $json['data'][0]);
    }

    public function testProjectReturnsDetailJson(): void
    {
        $project = $this->createProject(1, 'Portfolio Website', '2026-06-01');

        $repository = $this->createMock(ProjectRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($project);

        $controller = new ProjectApiController(
            new ResponseFactory(false, 'app/views'),
            $repository
        );

        $request = new Request('GET', '/api/projects/1', [], []);
        $request->routeParameters['id'] = '1';

        $response = $controller->project($request);

        $this->assertSame(200, $response->responseCode);

        $json = json_decode($response->body, true);

        $this->assertSame('projects', $json['meta']['resource']);
        $this->assertSame('/api/projects/1', $json['links']['self']);
        $this->assertSame('/api/projects', $json['links']['collection']);

        $this->assertSame(1, $json['data']['id']);
        $this->assertSame('Portfolio Website', $json['data']['title']);
        $this->assertSame('Long description for Portfolio Website', $json['data']['longDescription']);
        $this->assertSame(['PHP', 'Twig', 'SQLite'], $json['data']['tech']);
        $this->assertSame('Web Development', $json['data']['category']);
        $this->assertSame('/profile', $json['data']['url']);
    }

    public function testProjectReturnsJsonErrorWhenProjectDoesNotExist(): void
    {
        $repository = $this->createMock(ProjectRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $controller = new ProjectApiController(
            new ResponseFactory(false, 'app/views'),
            $repository
        );

        $request = new Request('GET', '/api/projects/999', [], []);
        $request->routeParameters['id'] = '999';

        $response = $controller->project($request);

        $this->assertSame(404, $response->responseCode);

        $json = json_decode($response->body, true);

        $this->assertSame('projects', $json['meta']['resource']);
        $this->assertSame('/api/projects/999', $json['links']['self']);
        $this->assertSame('/api/projects', $json['links']['collection']);
        $this->assertSame(404, $json['error']['status']);
        $this->assertSame('Project not found', $json['error']['message']);
    }

    public function testProjectKeepsInvalidDateWhenDateCannotBeFormatted(): void
    {
        $project = $this->createProject(1, 'Portfolio Website', 'not-a-real-date');

        $repository = $this->createMock(ProjectRepositoryInterface::class);

        $repository
            ->method('findById')
            ->with(1)
            ->willReturn($project);

        $controller = new ProjectApiController(
            new ResponseFactory(false, 'app/views'),
            $repository
        );

        $request = new Request('GET', '/api/projects/1', [], []);
        $request->routeParameters['id'] = '1';

        $response = $controller->project($request);

        $json = json_decode($response->body, true);

        $this->assertSame('not-a-real-date', $json['data']['date']);
    }

    private function createProject(int $id, string $title, string $completedAt): Project
    {
        $project = new Project();

        $project->id = $id;
        $project->title = $title;
        $project->description = 'Short description for ' . $title;
        $project->longDescription = 'Long description for ' . $title;
        $project->tech = ['PHP', 'Twig', 'SQLite'];
        $project->status = 'Completed';
        $project->completedAt = $completedAt;
        $project->category = 'Web Development';
        $project->image = '/uploads/projects/project.png';
        $project->url = '/profile';
        $project->sortOrder = 1;
        $project->createdAt = '2026-06-01 10:00:00';
        $project->updatedAt = '2026-06-01 10:00:00';

        return $project;
    }
}
