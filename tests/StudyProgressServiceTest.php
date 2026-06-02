<?php

use App\Models\StudyAssessment;
use App\Services\StudyProgressService;
use PHPUnit\Framework\TestCase;

class StudyProgressServiceTest extends TestCase
{
    public function testPassedGradesAddEarnedCredits(): void
    {
        $assessment1 = new StudyAssessment();
        $assessment1->earnableCredits = 5;
        $assessment1->grade = 7.0;

        $assessment2 = new StudyAssessment();
        $assessment2->earnableCredits = 2.5;
        $assessment2->grade = 5.5;

        $service = new StudyProgressService();

        $result = $service->calculate([$assessment1, $assessment2]);

        $this->assertEquals(7.5, $result['earnedCredits']);
        $this->assertEquals(7.5, $result['totalCredits']);
        $this->assertEquals(100.0, $result['progressPercentage']);
    }

    public function testFailedGradesDoNotAddCredits(): void
    {
        $assessment = new StudyAssessment();
        $assessment->earnableCredits = 5;
        $assessment->grade = 5.4;

        $service = new StudyProgressService();

        $result = $service->calculate([$assessment]);

        $this->assertEquals(0, $result['earnedCredits']);
        $this->assertEquals(5, $result['totalCredits']);
        $this->assertEquals(0.0, $result['progressPercentage']);
    }

    public function testNullGradeDoesNotAddCredits(): void
    {
        $assessment = new StudyAssessment();
        $assessment->earnableCredits = 5;
        $assessment->grade = null;

        $service = new StudyProgressService();

        $result = $service->calculate([$assessment]);

        $this->assertEquals(0, $result['earnedCredits']);
        $this->assertEquals(5, $result['totalCredits']);
    }
}
