<?php

namespace App\Services\Adaptive;

use App\Models\Attempt;
use App\Models\FeedbackReflection;
use App\Models\LearningObjective;
use App\Models\Mastery;
use App\Models\User;
use Carbon\CarbonInterface;

class MasteryScoringService
{
    public function updateForAttempt(Attempt $attempt): void
    {
        $attempt->loadMissing('item.learningObjective', 'feedback.reflections', 'user');

        $item = $attempt->item;
        $objective = $item?->learningObjective;

        if (! $objective instanceof LearningObjective) {
            return;
        }

        $user = $attempt->user;

        $mastery = Mastery::firstOrNew([
            'user_id' => $user->id,
            'objective_code' => $objective->code,
        ]);

        $previousScore = $mastery->score ?? 0.0;
        $attemptScore = $this->normaliseScore($attempt->score);
        $recencyFactor = $this->decayForTimestamp($attempt->created_at ?? now());
        $reflectionBonus = $this->reflectionBonus($attempt->feedback?->reflections->count() ?? 0);

        $newScore = round(
            ($attemptScore * 0.6)
            + ($previousScore * 0.3 * $recencyFactor)
            + $reflectionBonus,
            2
        );

        $mastery->fill([
            'level' => $this->levelForScore($newScore),
            'score' => $newScore,
            'last_seen_at' => $attempt->created_at ?? now(),
        ]);

        $mastery->save();
    }

    public function updateForReflection(FeedbackReflection $reflection): void
    {
        $feedback = $reflection->feedback;
        $attempt = $feedback?->attempt;

        if (! $attempt) {
            return;
        }

        $this->updateForAttempt($attempt->loadMissing('feedback.reflections'));
    }

    public function updateFromQuiz(User $user, string $objectiveCode, float $score): void
    {
        $objective = LearningObjective::where('code', $objectiveCode)->first();

        if (! $objective) {
            return;
        }

        $mastery = Mastery::firstOrNew([
            'user_id' => $user->id,
            'objective_code' => $objectiveCode,
        ]);

        $previous = $mastery->score ?? 0.0;
        $newScore = round(($previous * 0.5) + ($score * 0.5), 2);

        $mastery->fill([
            'level' => $this->levelForScore($newScore),
            'score' => $newScore,
            'last_seen_at' => now(),
        ]);

        $mastery->save();
    }

    protected function normaliseScore(?float $score): float
    {
        if ($score === null) {
            return 0.0;
        }

        return max(0.0, min(100.0, $score));
    }

    protected function decayForTimestamp(CarbonInterface $timestamp): float
    {
        $hours = max(0, $timestamp->diffInHours(now()));

        return exp(-$hours / 168); // half-life of one week
    }

    protected function reflectionBonus(int $count): float
    {
        if ($count <= 0) {
            return 0.0;
        }

        return min(3, $count) * 2.5; // up to +7.5
    }

    protected function levelForScore(float $score): string
    {
        return match (true) {
            $score >= 95 => 'mastery',
            $score >= 85 => 'strong',
            $score >= 75 => 'proficient',
            $score >= 60 => 'competent',
            $score >= 40 => 'developing',
            default => 'emerging',
        };
    }
}
