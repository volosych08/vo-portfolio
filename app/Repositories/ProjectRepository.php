<?php

namespace App\Repositories;

use App\Models\Project;
use Framework\Database;

class ProjectRepository implements ProjectRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Project[]
     */
    public function all(): array
    {
        $rows = $this->database
            ->run("SELECT * FROM projects ORDER BY sort_order")
            ->fetchAll();

        $projects = [];

        foreach ($rows as $row) {
            $projects[] = $this->fromDbRow($row);
        }

        return $projects;
    }

    public function findById(int $id): ?Project
    {
        $row = $this->database
            ->run(
                "SELECT * FROM projects WHERE id = :id",
                [
                    'id' => $id,
                ]
            )
            ->fetch();

        if (!$row) {
            return null;
        }

        return $this->fromDbRow($row);
    }

    public function insert(Project $project): Project
    {
        $techJson = json_encode($project->tech, JSON_THROW_ON_ERROR);

        $this->database->run(
            "INSERT INTO projects (
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
        ) VALUES (
            :title,
            :description,
            :longDescription,
            :techJson,
            :status,
            :completedAt,
            :category,
            :image,
            :url,
            :sortOrder,
            :createdAt,
            :updatedAt
        )",
            [
                'title' => $project->title,
                'description' => $project->description,
                'longDescription' => $project->longDescription,
                'techJson' => $techJson,
                'status' => $project->status,
                'completedAt' => $project->completedAt,
                'category' => $project->category,
                'image' => $project->image,
                'url' => $project->url,
                'sortOrder' => $project->sortOrder,
                'createdAt' => $project->createdAt,
                'updatedAt' => $project->updatedAt,
            ]
        );

        $project->id = $this->database->getLastID();

        return $project;
    }

    private function fromDbRow(mixed $row): Project
    {
        $decodedTech = json_decode($row->tech_json, true);

        $project = new Project();

        $project->id = (int) $row->id;
        $project->title = $row->title;
        $project->description = $row->description;
        $project->longDescription = $row->long_description;
        $project->tech = is_array($decodedTech) ? $decodedTech : [];
        $project->status = $row->status;
        $project->completedAt = $row->completed_at;
        $project->category = $row->category;
        $project->image = $row->image;
        $project->url = $row->url;
        $project->sortOrder = (int) $row->sort_order;
        $project->createdAt = $row->created_at;
        $project->updatedAt = $row->updated_at;

        return $project;
    }
}
