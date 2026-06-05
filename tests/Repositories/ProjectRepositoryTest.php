<?php

namespace Repositories;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class ProjectRepositoryTest extends TestCase
{
    private ProjectRepository $repository;

    protected function setUp(): void
    {
        $database = $this->createTestDatabase();

        $database->run("DROP TABLE IF EXISTS projects");

        $database->run("
            CREATE TABLE projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                long_description TEXT NOT NULL,
                tech_json TEXT NOT NULL,
                status TEXT NOT NULL,
                completed_at TEXT NOT NULL,
                category TEXT NOT NULL,
                image TEXT NOT NULL,
                url TEXT NOT NULL,
                sort_order INTEGER NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        $database->run("
            INSERT INTO projects (
                title,
                description,
                long_description,
                tech_json,
                status,
                completed_at,
                category,
                image,
                url,
                sort_order,
                created_at,
                updated_at
            ) VALUES
            (
                'Portfolio Website',
                'Short portfolio description',
                'Long portfolio description',
                '[\"PHP\", \"Twig\", \"SQLite\"]',
                'Completed',
                '2026-06-01',
                'Web Development',
                '/uploads/projects/portfolio.png',
                '/profile',
                2,
                '2026-06-01 10:00:00',
                '2026-06-01 10:00:00'
            ),
            (
                'Study Dashboard',
                'Short dashboard description',
                'Long dashboard description',
                '[\"PHP\", \"SQLite\", \"PHPUnit\"]',
                'In Progress',
                '2026-06-02',
                'Study Progress',
                '/uploads/projects/dashboard.png',
                '/dashboard',
                1,
                '2026-06-02 10:00:00',
                '2026-06-02 10:00:00'
            )
        ");

        $this->repository = new ProjectRepository($database);
    }

    public function testAllReturnsProjectsOrderedBySortOrder(): void
    {
        $projects = $this->repository->all();

        $this->assertCount(2, $projects);
        $this->assertSame('Study Dashboard', $projects[0]->title);
        $this->assertSame('Portfolio Website', $projects[1]->title);
    }

    public function testFindByIdReturnsProject(): void
    {
        $project = $this->repository->findById(1);

        $this->assertNotNull($project);
        $this->assertSame('Portfolio Website', $project->title);
        $this->assertSame('Long portfolio description', $project->longDescription);
        $this->assertSame(['PHP', 'Twig', 'SQLite'], $project->tech);
        $this->assertSame('/uploads/projects/portfolio.png', $project->image);
    }

    public function testFindByIdReturnsNullWhenProjectDoesNotExist(): void
    {
        $project = $this->repository->findById(999);

        $this->assertNull($project);
    }

    /**
     * @throws \JsonException
     */
    public function testInsertCreatesNewProject(): void
    {
        $project = new Project();
        $project->title = '3D Laptop Showcase';
        $project->description = 'Interactive 3D project showcase';
        $project->longDescription = 'A Three.js laptop showcase that loads project data from an API.';
        $project->tech = ['Three.js', 'JavaScript', 'REST API'];
        $project->status = 'Completed';
        $project->completedAt = '2026-06-03';
        $project->category = 'Innovation';
        $project->image = '/uploads/projects/laptop.png';
        $project->url = '/projects-3d';
        $project->sortOrder = 3;
        $project->createdAt = '2026-06-03 10:00:00';
        $project->updatedAt = '2026-06-03 10:00:00';

        $insertedProject = $this->repository->insert($project);

        $this->assertGreaterThan(0, $insertedProject->id);

        $foundProject = $this->repository->findById($insertedProject->id);

        $this->assertNotNull($foundProject);
        $this->assertSame('3D Laptop Showcase', $foundProject->title);
        $this->assertSame(['Three.js', 'JavaScript', 'REST API'], $foundProject->tech);
        $this->assertSame('/projects-3d', $foundProject->url);
    }

    private function createTestDatabase(): Database
    {
        return new Database('sqlite::memory:');
    }
}
