<?php

namespace Controllers;

use App\Controllers\DashboardController;
use App\Models\StudyAssessment;
use App\Repositories\StudyAssessmentRepositoryInterface;
use App\Services\StudyProgressService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;

class DashboardControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
    }

    public function testIndexRendersDashboardWithProgressData(): void
    {
        $assessments = [
            $this->createAssessment(1, 'Programming Basics', 'PBA', 'Exam 1', 5.0, 7.0),
            $this->createAssessment(2, 'Programming Basics', 'PBA', 'Exam 2', 5.0, null),
        ];

        $repository = $this->createMock(StudyAssessmentRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn($assessments);

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'dashboard/index.html.twig',
                $this->callback(function (array $context): bool {
                    return $context['earnedCredits'] === 5.0
                        && $context['totalCredits'] === 10.0
                        && $context['requiredCredits'] === 45
                        && $context['progressPercentage'] === 50.0
                        && $context['requiredPercentage'] === 450.0
                        && $context['courseRowspans']['Programming Basics|PBA'] === 2;
                })
            )
            ->willReturn(new Response('dashboard'));

        $controller = new DashboardController(
            $responseFactory,
            $repository,
            new StudyProgressService()
        );

        $response = $controller->index();

        $this->assertSame('dashboard', $response->body);
    }

    public function testEditRendersEditPage(): void
    {
        $assessments = [
            $this->createAssessment(1, 'Programming Basics', 'PBA', 'Exam 1', 5.0, 7.0),
        ];

        $repository = $this->createMock(StudyAssessmentRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn($assessments);

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'dashboard/edit.html.twig',
                $this->callback(function (array $context): bool {
                    return $context['errors'] === []
                        && $context['courseRowspans']['Programming Basics|PBA'] === 1;
                })
            )
            ->willReturn(new Response('edit page'));

        $controller = new DashboardController(
            $responseFactory,
            $repository,
            new StudyProgressService()
        );

        $response = $controller->edit();

        $this->assertSame('edit page', $response->body);
    }

    public function testUpdateSavesValidGradesAndRedirects(): void
    {
        $_POST['grades'] = [
            1 => '8,5',
            2 => '',
        ];

        $assessments = [
            $this->createAssessment(1, 'Programming Basics', 'PBA', 'Exam 1', 5.0, 7.0),
            $this->createAssessment(2, 'Computer Science', 'CSB', 'Exam 1', 2.5, null),
        ];

        $repository = $this->createMock(StudyAssessmentRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn($assessments);

        $repository
            ->expects($this->exactly(2))
            ->method('updateGrade')
            ->willReturnCallback(function (int $id, ?float $grade): bool {
                if ($id === 1) {
                    $this->assertSame(8.5, $grade);
                }

                if ($id === 2) {
                    $this->assertNull($grade);
                }

                return true;
            });

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/dashboard')
            ->willReturn(new Response('', 302, 'Location: /dashboard'));

        $controller = new DashboardController(
            $responseFactory,
            $repository,
            new StudyProgressService()
        );

        $response = $controller->update(new Request('POST', '/dashboard/edit', [], []));

        $this->assertSame(302, $response->responseCode);
    }

    public function testUpdateReturnsErrorsForInvalidGrades(): void
    {
        $_POST['grades'] = [
            1 => 'abc',
            2 => '11',
        ];

        $assessments = [
            $this->createAssessment(1, 'Programming Basics', 'PBA', 'Exam 1', 5.0, 7.0),
            $this->createAssessment(2, 'Computer Science', 'CSB', 'Exam 1', 2.5, null),
        ];

        $repository = $this->createMock(StudyAssessmentRepositoryInterface::class);

        $repository
            ->expects($this->once())
            ->method('all')
            ->willReturn($assessments);

        $repository
            ->expects($this->never())
            ->method('updateGrade');

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'dashboard/edit.html.twig',
                $this->callback(function (array $context): bool {
                    return count($context['errors']) === 2
                        && str_contains($context['errors'][0], 'must be a number')
                        && str_contains($context['errors'][1], 'must be between 1 and 10');
                })
            )
            ->willReturn(new Response('errors'));

        $controller = new DashboardController(
            $responseFactory,
            $repository,
            new StudyProgressService()
        );

        $response = $controller->update(new Request('POST', '/dashboard/edit', [], []));

        $this->assertSame('errors', $response->body);
    }

    private function createAssessment(
        int $id,
        string $courseName,
        string $courseCode,
        string $examName,
        float $credits,
        ?float $grade
    ): StudyAssessment {
        $assessment = new StudyAssessment();

        $assessment->id = $id;
        $assessment->quartile = 'Q1';
        $assessment->courseName = $courseName;
        $assessment->courseCode = $courseCode;
        $assessment->examName = $examName;
        $assessment->examDate = '2026-06-01';
        $assessment->earnableCredits = $credits;
        $assessment->grade = $grade;
        $assessment->sortOrder = $id;
        $assessment->createdAt = '2026-06-01 10:00:00';
        $assessment->updatedAt = '2026-06-01 10:00:00';

        return $assessment;
    }
}
