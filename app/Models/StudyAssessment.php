<?php

namespace App\Models;

class StudyAssessment
{
    public int $id;

    public string $quartile;

    public string $courseName;

    public string $courseCode;

    public string $examName;

    public string $examDate;

    public float $earnableCredits;

    public ?float $grade;

    public int $sortOrder;

    public string $createdAt;

    public string $updatedAt;
}
