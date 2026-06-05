<?php

namespace Repositories;

use App\Repositories\StudyAssessmentRepository;
use Framework\Database;
use PHPUnit\Framework\TestCase;

class StudyAssessmentRepositoryTest extends TestCase
{
    private StudyAssessmentRepository $repository;

    protected function setUp(): void
    {
        $database = $this->createTestDatabase();

        $database->run("DROP TABLE IF EXISTS study_assessments");

        $database->run("
            CREATE TABLE study_assessments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quartile TEXT NOT NULL,
                course_name TEXT NOT NULL,
                course_code TEXT NOT NULL,
                exam_name TEXT NOT NULL,
                exam_date TEXT NOT NULL,
                earnable_credits REAL NOT NULL,
                grade REAL NULL,
                sort_order INTEGER NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        $database->run("
            INSERT INTO study_assessments (
                quartile,
                course_name,
                course_code,
                exam_name,
                exam_date,
                earnable_credits,
                grade,
                sort_order,
                created_at,
                updated_at
            ) VALUES
            (
                'Q1',
                'Programming Basics',
                'PBA',
                'Written Exam',
                '2026-01-10',
                5.0,
                7.5,
                2,
                '2026-06-01 10:00:00',
                '2026-06-01 10:00:00'
            ),
            (
                'Q1',
                'Computer Science Basics',
                'CSB',
                'Written Exam',
                '2026-01-11',
                2.5,
                NULL,
                1,
                '2026-06-01 10:00:00',
                '2026-06-01 10:00:00'
            )
        ");

        $this->repository = new StudyAssessmentRepository($database);
    }

    public function testAllReturnsAssessmentsOrderedBySortOrder(): void
    {
        $assessments = $this->repository->all();

        $this->assertCount(2, $assessments);
        $this->assertSame('Computer Science Basics', $assessments[0]->courseName);
        $this->assertSame('Programming Basics', $assessments[1]->courseName);
    }

    public function testAllMapsNullGradeCorrectly(): void
    {
        $assessments = $this->repository->all();

        $this->assertNull($assessments[0]->grade);
        $this->assertSame(2.5, $assessments[0]->earnableCredits);
    }

    public function testAllMapsFloatGradeCorrectly(): void
    {
        $assessments = $this->repository->all();

        $this->assertSame(7.5, $assessments[1]->grade);
        $this->assertSame(5.0, $assessments[1]->earnableCredits);
    }

    public function testUpdateGradeChangesGrade(): void
    {
        $updated = $this->repository->updateGrade(2, 8.0);

        $this->assertTrue($updated);

        $assessments = $this->repository->all();

        $this->assertSame(8.0, $assessments[0]->grade);
    }

    public function testUpdateGradeCanSetGradeToNull(): void
    {
        $updated = $this->repository->updateGrade(1, null);

        $this->assertTrue($updated);

        $assessments = $this->repository->all();

        $this->assertNull($assessments[1]->grade);
    }

    public function testUpdateGradeReturnsFalseWhenAssessmentDoesNotExist(): void
    {
        $updated = $this->repository->updateGrade(999, 7.0);

        $this->assertFalse($updated);
    }

    private function createTestDatabase(): Database
    {
        return new Database('sqlite::memory:');
    }
}
