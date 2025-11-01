<?php

namespace App\Filament\Widgets;

use App\Models\QuizSession;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class QuizOutcomesWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // Total quizzes submitted in last 7 days
        $totalQuizzes = QuizSession::where('status', 'submitted')
            ->where('submitted_at', '>=', $sevenDaysAgo)
            ->count();

        // Average score across all submitted quizzes (last 7 days)
        $averageScore = QuizSession::where('status', 'submitted')
            ->where('submitted_at', '>=', $sevenDaysAgo)
            ->whereNotNull('average_score')
            ->avg('average_score');

        // Pending reviews
        $pendingReviews = QuizSession::where('status', 'submitted')
            ->where('pending_review_count', '>', 0)
            ->whereDoesntHave('review', function ($query) {
                $query->whereIn('status', ['reviewed', 'approved']);
            })
            ->count();

        // Quiz performance by objective (last 7 days)
        $objectivePerformance = DB::table('quiz_session_items')
            ->join('quiz_sessions', 'quiz_session_items.quiz_session_id', '=', 'quiz_sessions.id')
            ->join('items', 'quiz_session_items.item_id', '=', 'items.id')
            ->where('quiz_sessions.status', 'submitted')
            ->where('quiz_sessions.submitted_at', '>=', $sevenDaysAgo)
            ->whereNotNull('quiz_session_items.score')
            ->select('items.objective_code', DB::raw('AVG(quiz_session_items.score) as avg_score'))
            ->groupBy('items.objective_code')
            ->orderBy('avg_score', 'asc')
            ->first();

        $lowestObjective = $objectivePerformance ? $objectivePerformance->objective_code : 'N/A';
        $lowestScore = $objectivePerformance ? round($objectivePerformance->avg_score, 1) : null;

        return [
            Stat::make('Quizzes Submitted (7 days)', $totalQuizzes)
                ->description('Total quizzes completed')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Average Score', $averageScore ? round($averageScore, 1).'%' : 'N/A')
                ->description('Across all quizzes (7 days)')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($averageScore >= 75 ? 'success' : ($averageScore >= 60 ? 'warning' : 'danger')),

            Stat::make('Pending Reviews', $pendingReviews)
                ->description('SAQ items awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingReviews > 0 ? 'warning' : 'success'),

            Stat::make('Lowest Objective', $lowestObjective)
                ->description($lowestScore ? "Avg: {$lowestScore}%" : 'No data')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
