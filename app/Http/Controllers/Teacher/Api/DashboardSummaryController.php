<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Feedback;
use App\Models\Mastery;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardSummaryController extends Controller
{
    public function __invoke(Request $request): array
    {
        $now = CarbonImmutable::now();

        $attemptsToday = Attempt::query()
            ->whereDate('created_at', $now->toDateString())
            ->count();

        $attemptsLast7 = Attempt::query()
            ->where('created_at', '>=', $now->subDays(7))
            ->count();

        $pendingFeedback = Feedback::query()
            ->where('status', '!=', 'published')
            ->count();

        $feedbackPublishedLast7 = Feedback::query()
            ->where('status', 'published')
            ->where(function ($query) use ($now): void {
                $query->where('released_at', '>=', $now->subDays(7))
                    ->orWhere(function ($inner) use ($now): void {
                        $inner->whereNull('released_at')
                            ->where('updated_at', '>=', $now->subDays(7));
                    });
            })
            ->count();

        $studentsTotal = User::query()
            ->where('role', 'student')
            ->count();

        $levelCase = "CASE level "
            . " WHEN 'emerging' THEN 1"
            . " WHEN 'developing' THEN 2"
            . " WHEN 'competent' THEN 3"
            . " WHEN 'proficient' THEN 4"
            . " WHEN 'strong' THEN 5"
            . " WHEN 'mastery' THEN 6"
            . " ELSE NULL END";

        $masteryAverages = Mastery::query()
            ->select([
                'objective_code',
                DB::raw("AVG($levelCase) as average_level"),
                DB::raw('COUNT(*) as samples'),
            ])
            ->groupBy('objective_code')
            ->orderByDesc(DB::raw('AVG(' . $levelCase . ')'))
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $average = (float) $row->average_level;
                $percent = $average > 0 ? round(($average / 6) * 100) : null;
                $labels = [
                    0 => 'Unknown',
                    1 => 'Emerging',
                    2 => 'Developing',
                    3 => 'Competent',
                    4 => 'Proficient',
                    5 => 'Strong',
                    6 => 'Mastery',
                ];

                $nearest = (int) round($average);

                return [
                    'objective_code' => $row->objective_code,
                    'average_percent' => $percent,
                    'level_label' => $labels[$nearest] ?? 'Unknown',
                    'samples' => (int) $row->samples,
                ];
            });

        $recentPending = Feedback::query()
            ->with(['attempt.user'])
            ->where('status', '!=', 'published')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Feedback $feedback) => [
                'id' => $feedback->id,
                'student' => $feedback->attempt?->user?->name ?? 'Unknown student',
                'objective_code' => $feedback->attempt?->item?->objective_code,
                'submitted_at' => optional($feedback->created_at)->toIso8601String(),
                'status' => $feedback->status,
            ]);

        return [
            'snapshot' => [
                'attempts_today' => $attemptsToday,
                'attempts_last_7_days' => $attemptsLast7,
                'students_total' => $studentsTotal,
                'pending_feedback' => $pendingFeedback,
                'feedback_published_last_7_days' => $feedbackPublishedLast7,
            ],
            'mastery' => $masteryAverages,
            'pending_feedback_preview' => $recentPending,
            'generated_at' => $now->toIso8601String(),
        ];
    }
}
