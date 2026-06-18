<?php

namespace App\Repositories;

use App\Models\StudyAssessment;

interface StudyAssessmentRepositoryInterface
{
    /**
     * @return StudyAssessment[]
     */
    public function all(): array;

    public function updateGrade(int $id, ?float $grade): bool;
}
