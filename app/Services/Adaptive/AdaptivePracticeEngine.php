<?php

namespace App\Services\Adaptive;

use App\Models\AdaptiveRecommendationEvent;
use App\Models\Item;
use App\Models\LearningObjective;
use App\Models\User;
use Illuminate\Support\Collection;

class AdaptivePracticeEngine
{
    public function __construct(
        protected ExperimentManager $experiments,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recommendationsFor(User $user, int $limit = 10): array
    {
        $bundle = $this->bundle($user, $limit);

        return $bundle['items']
            ->map(fn (array $data) => $this->transformRecommendation(
                $user,
                $bundle['assignment']->experiment_key,
                $bundle['assignment']->variant,
                $data
            ))
            ->values()
            ->all();
    }

    public function bundle(User $user, int $limit = 10, array $objectiveCodes = [], bool $onlyQuizEligible = false): array
    {
        $assignment = $this->experiments->assign($user, 'practice_engine_v1', ['balanced', 'explore']);

        $scored = $this->scoredItems($user, $objectiveCodes, $onlyQuizEligible);
        $ranked = $this->rankItems($scored, $assignment->variant);

        return [
            'assignment' => $assignment,
            'items' => $ranked->take($limit)->values(),
        ];
    }

    protected function scoredItems(User $user, array $objectiveCodes = [], bool $onlyQuizEligible = false): Collection
    {
        $masteries = $user->masteries()
            ->select(['objective_code', 'score', 'level', 'last_seen_at'])
            ->get()
            ->keyBy('objective_code');

        $itemsQuery = Item::query()
            ->with(['learningObjective', 'rubric'])
            ->whereNotNull('learning_objective_id');

        if ($onlyQuizEligible) {
            $itemsQuery->where('is_quiz_eligible', true);
        }

        if (! empty($objectiveCodes)) {
            $itemsQuery->whereHas('learningObjective', function ($query) use ($objectiveCodes): void {
                $query->whereIn('code', $objectiveCodes);
            });
        }

        return $itemsQuery->get()->map(function (Item $item) use ($masteries) {
            /** @var LearningObjective|null $objective */
            $objective = $item->learningObjective;
            $mastery = $masteries->get($objective?->code);

            $score = $mastery?->score ?? null;
            $priority = $this->priorityForItem($score, $item, $mastery?->last_seen_at);

            return [
                'item' => $item,
                'objective' => $objective,
                'mastery' => $mastery,
                'priority' => $priority,
            ];
        });
    }

    protected function rankItems(Collection $scored, string $variant): Collection
    {
        return match ($variant) {
            'explore' => $this->rankExplore($scored),
            default => $this->rankBalanced($scored),
        };
    }

    protected function rankBalanced(Collection $scored): Collection
    {
        return $scored->sortBy([
            fn ($a, $b) => $b['priority'] <=> $a['priority'],
            fn ($a, $b) => ($a['mastery']->score ?? 0) <=> ($b['mastery']->score ?? 0),
        ])->values();
    }

    protected function rankExplore(Collection $scored): Collection
    {
        $sorted = $scored->sortByDesc('priority')->values();
        $top = $sorted->take(5);
        $unseen = $scored->filter(fn ($entry) => empty($entry['mastery']))
            ->shuffle()
            ->take(5);

        return $top
            ->concat($unseen)
            ->unique(fn ($entry) => $entry['item']->id)
            ->values();
    }

    protected function priorityForItem(?float $score, Item $item, ?\Illuminate\Support\Carbon $lastSeen): float
    {
        $base = match (true) {
            $score === null => 1.0,
            $score < 40 => 0.9,
            $score < 60 => 0.7,
            $score < 80 => 0.5,
            $score < 90 => 0.3,
            default => 0.1,
        };

        $recencyPenalty = $lastSeen ? max(0.0, min(0.5, $lastSeen->diffInDays(now()) / 14)) : 0.4;

        $difficulty = $item->meta['difficulty'] ?? null;
        $difficultyBoost = match ($difficulty) {
            'hard' => 0.05,
            'easy' => -0.05,
            default => 0,
        };

        return round($base + $recencyPenalty + $difficultyBoost, 4);
    }

    /**
     * Transform recommendation for API and log event.
     */
    protected function transformRecommendation(User $user, string $experimentKey, string $variant, array $data): array
    {
        /** @var Item $item */
        $item = $data['item'];
        $objective = $data['objective'];
        $mastery = $data['mastery'];

        $reason = $this->reasonFor($variant, $mastery?->score, $objective?->title);

        AdaptiveRecommendationEvent::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'experiment_key' => $experimentKey,
            'variant' => $variant,
            'event_type' => 'surface',
            'score_before' => $mastery->score ?? null,
            'meta' => [
                'objective_code' => $objective?->code,
            ],
            'occurred_at' => now(),
        ]);

        return [
            'id' => $item->id,
            'objective_code' => $objective?->code ?? $item->objective_code,
            'stem' => $item->stem,
            'type' => $item->type,
            'rubric' => [
                'name' => $item->rubric?->name,
                'criteria' => $item->rubric?->criteria,
                'levels' => $item->rubric?->levels,
            ],
            'meta' => $item->meta,
            'priority' => $data['priority'],
            'recommended_level' => $mastery->level ?? 'unseen',
            'reason' => $reason,
        ];
    }

    protected function reasonFor(string $variant, ?float $score, ?string $objectiveTitle): string
    {
        $title = $objectiveTitle ?? 'this objective';

        return match ($variant) {
            'explore' => "Explore a new angle on {$title}.",
            default => $score === null
                ? "Build initial understanding of {$title}."
                : "Boost mastery for {$title} (current score: ".round($score).").",
        };
    }
}
