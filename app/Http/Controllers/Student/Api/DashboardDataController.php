<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardDataController extends Controller
{
    public function __invoke(Request $request): array
    {
        $student = $request->user();

        $masteries = $student->masteries()
            ->orderBy('objective_code')
            ->get()
            ->map(fn ($mastery) => [
                'objective_code' => $mastery->objective_code,
                'level' => $mastery->level,
                'progress_percent' => $this->progressForLevel($mastery->level),
                'last_seen_at' => optional($mastery->last_seen_at)->toIso8601String(),
            ]);

        /** @var Recommendation|null $featured */
        $featured = $student->recommendations()
            ->where('chosen', true)
            ->latest('created_at')
            ->first();

        $activity = $student->attempts()
            ->with(['item', 'feedback'])
            ->latest('created_at')
            ->take(12)
            ->get()
            ->flatMap(function ($attempt) {
                $entries = collect([
                    [
                        'type' => 'attempt',
                        'title' => 'Attempted '.$attempt->item?->objective_code,
                        'subtitle' => Str::limit($attempt->item?->stem ?? '', 80),
                        'score' => $attempt->score,
                        'occurred_at' => optional($attempt->created_at)->toIso8601String(),
                    ],
                ]);

                if ($attempt->feedback) {
                    $entries->push([
                        'type' => 'feedback',
                        'title' => ucfirst($attempt->feedback->status).' feedback posted',
                        'subtitle' => $attempt->feedback->ai_text['summary'] ?? null,
                        'occurred_at' => optional(
                            $attempt->feedback->released_at ?? $attempt->feedback->created_at
                        )->toIso8601String(),
                    ]);
                }

                return $entries;
            })
            ->sortByDesc('occurred_at')
            ->values();

        return [
            'mastery' => $masteries,
            'featuredRecommendation' => $featured?->toArray(),
            'activity' => $activity,
        ];
    }

    protected function progressForLevel(?string $level): int
    {
        return [
            'emerging' => 35,
            'developing' => 55,
            'competent' => 65,
            'proficient' => 75,
            'strong' => 85,
            'mastery' => 100,
        ][$level ?? ''] ?? 50;
    }
}
