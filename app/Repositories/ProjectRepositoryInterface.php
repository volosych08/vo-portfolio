<?php

namespace App\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    /**
     * @return Project[]
     */
    public function all(): array;
    public function findById(int $id): ?Project;
    public function insert(Project $project): ?Project;
}
