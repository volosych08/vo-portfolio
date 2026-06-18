<?php

namespace App\Services;

class StudyProgressService
{
    /**
     * @param array<int, mixed> $assessments
     * @return array<string, float>
     */
    public function calculate(array $assessments): array
    {
        $totalCredits = 0;
        $earnedCredits = 0;
        $requiredCredits = 45;

        foreach ($assessments as $assessment) {
            $totalCredits += $assessment->earnableCredits;

            if ($assessment->grade !== null && $assessment->grade >= 5.5) {
                $earnedCredits += $assessment->earnableCredits;
            }
        }

        $progressPercentage = 0;
        $requiredPercentage = 0;

        if ($totalCredits > 0) {
            $progressPercentage = ($earnedCredits / $totalCredits) * 100;
            $requiredPercentage = ($requiredCredits / $totalCredits) * 100;
        }

        return [
            'earnedCredits' => $earnedCredits,
            'totalCredits' => $totalCredits,
            'requiredCredits' => $requiredCredits,
            'progressPercentage' => round($progressPercentage, 1),
            'requiredPercentage' => round($requiredPercentage, 1),
        ];
    }
}
