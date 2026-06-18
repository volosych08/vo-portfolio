<?php

namespace App\Repositories;

use App\Models\StudyAssessment;
use Framework\Database;

class StudyAssessmentRepository implements StudyAssessmentRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function all(): array
    {
        $stmt = $this->database
            ->run("SELECT * FROM study_assessments ORDER BY sort_order")
            ->fetchAll();

        $assessments = [];

        foreach ($stmt as $row) {
            $assessments[] = $this->fromDbRow($row);
        }

        return $assessments;
    }

    public function updateGrade(int $id, ?float $grade): bool
    {
        $stmt = $this->database->run(
            "UPDATE study_assessments
             SET grade = :grade,
                 updated_at = :updatedAt
             WHERE id = :id",
            [
                'id' => $id,
                'grade' => $grade,
                'updatedAt' => date('Y-m-d H:i:s'),
            ]
        );

        return $stmt->rowCount() > 0;
    }

    private function fromDbRow(mixed $row): StudyAssessment
    {
        $assessment = new StudyAssessment();

        $assessment->id = $row->id;
        $assessment->quartile = $row->quartile;
        $assessment->courseName = $row->course_name;
        $assessment->courseCode = $row->course_code;
        $assessment->examName = $row->exam_name;
        $assessment->examDate = $row->exam_date;
        $assessment->earnableCredits = (float) $row->earnable_credits;
        $assessment->grade = $row->grade === null ? null : (float) $row->grade;
        $assessment->sortOrder = $row->sort_order;
        $assessment->createdAt = $row->created_at;
        $assessment->updatedAt = $row->updated_at;

        return $assessment;
    }
}
