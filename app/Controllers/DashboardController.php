<?php

namespace App\Controllers;

use App\Repositories\StudyAssessmentRepositoryInterface;
use App\Services\StudyProgressService;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class DashboardController
{
    private ResponseFactory $responseFactory;

    private StudyAssessmentRepositoryInterface $studyAssessmentRepository;

    private StudyProgressService $studyProgressService;

    public function __construct(
        ResponseFactory $responseFactory,
        StudyAssessmentRepositoryInterface $studyAssessmentRepository,
        StudyProgressService $studyProgressService
    ) {
        $this->responseFactory = $responseFactory;
        $this->studyAssessmentRepository = $studyAssessmentRepository;
        $this->studyProgressService = $studyProgressService;
    }

    public function index(): Response
    {
        $assessments = $this->studyAssessmentRepository->all();

        $progressData = $this->studyProgressService->calculate($assessments);

        return $this->responseFactory->view('dashboard/index.html.twig', [
            'assessments' => $assessments,
            'courseRowspans' => $this->calculateCourseRowspans($assessments),

            'earnedCredits' => $progressData['earnedCredits'],
            'totalCredits' => $progressData['totalCredits'],
            'requiredCredits' => $progressData['requiredCredits'],
            'progressPercentage' => $progressData['progressPercentage'],
            'requiredPercentage' => $progressData['requiredPercentage'],
        ]);
    }

    public function edit(): Response
    {
        $assessments = $this->studyAssessmentRepository->all();

        return $this->responseFactory->view('dashboard/edit.html.twig', [
            'assessments' => $assessments,
            'courseRowspans' => $this->calculateCourseRowspans($assessments),
            'errors' => [],
        ]);
    }

    public function update(Request $request): Response
    {
        $assessments = $this->studyAssessmentRepository->all();

        $submittedGrades = $_POST['grades'] ?? [];

        if (!is_array($submittedGrades)) {
            $submittedGrades = [];
        }

        $errors = [];
        $validatedGrades = [];

        foreach ($assessments as $assessment) {
            $rawGrade = $submittedGrades[$assessment->id] ?? '';
            $rawGrade = trim((string) $rawGrade);

            if ($rawGrade === '') {
                $validatedGrades[$assessment->id] = null;
                continue;
            }

            $rawGrade = str_replace(',', '.', $rawGrade);

            if (!is_numeric($rawGrade)) {
                $errors[] = 'Grade for ' . $assessment->courseName .
                    ' - ' . $assessment->examName . ' must be a number.';
                continue;
            }

            $grade = (float) $rawGrade;

            if ($grade < 1 || $grade > 10) {
                $errors[] = 'Grade for ' . $assessment->courseName .
                    ' - ' . $assessment->examName . ' must be between 1 and 10.';
                continue;
            }

            $validatedGrades[$assessment->id] = $grade;
        }

        if (!empty($errors)) {
            return $this->responseFactory->view('dashboard/edit.html.twig', [
                'assessments' => $assessments,
                'courseRowspans' => $this->calculateCourseRowspans($assessments),
                'errors' => $errors,
            ]);
        }

        foreach ($validatedGrades as $id => $grade) {
            $this->studyAssessmentRepository->updateGrade((int) $id, $grade);
        }

        return $this->responseFactory->redirect('/dashboard');
    }

    /**
     * @param array<int, mixed> $assessments
     * @return array<string, int>
     */
    private function calculateCourseRowspans(array $assessments): array
    {
        $rowspans = [];

        foreach ($assessments as $assessment) {
            $key = $assessment->courseName . '|' . $assessment->courseCode;

            if (!isset($rowspans[$key])) {
                $rowspans[$key] = 0;
            }

            $rowspans[$key]++;
        }

        return $rowspans;
    }
}
