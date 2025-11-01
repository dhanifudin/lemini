<?php

namespace App\Filament\Widgets;

use App\Models\QuizSession;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuizVariantComparisonWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $sessions = QuizSession::where('status', 'submitted')
            ->where('submitted_at', '>=', $sevenDaysAgo)
            ->whereNotNull('average_score')
            ->get();

        $immediate = $sessions->filter(fn ($s) => ($s->settings['feedback_variant'] ?? null) === 'immediate');
        $delayed = $sessions->filter(fn ($s) => ($s->settings['feedback_variant'] ?? null) === 'delayed');

        $immediateAvg = $immediate->count() > 0 ? round($immediate->avg('average_score'), 1) : 0;
        $delayedAvg = $delayed->count() > 0 ? round($delayed->avg('average_score'), 1) : 0;

        $immediateCompletionTime = $immediate->count() > 0
            ? round($immediate->map(fn ($s) => $s->started_at->diffInMinutes($s->submitted_at))->avg(), 1)
            : 0;

        $delayedCompletionTime = $delayed->count() > 0
            ? round($delayed->map(fn ($s) => $s->started_at->diffInMinutes($s->submitted_at))->avg(), 1)
            : 0;

        $stats = [
            Stat::make('Immediate Feedback (Variant A)', $immediate->count().' sessions')
                ->description("Avg Score: {$immediateAvg}%")
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info')
                ->chart($this->getWeeklyTrend($immediate)),

            Stat::make('Delayed Feedback (Variant B)', $delayed->count().' sessions')
                ->description("Avg Score: {$delayedAvg}%")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart($this->getWeeklyTrend($delayed)),

            Stat::make('Performance Delta', ($immediateAvg - $delayedAvg) > 0 ? '+' : ''.round($immediateAvg - $delayedAvg, 1).'%')
                ->description('Immediate vs Delayed')
                ->descriptionIcon($immediateAvg > $delayedAvg ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($immediateAvg > $delayedAvg ? 'success' : 'danger'),

            Stat::make('Avg Completion Time', round(($immediateCompletionTime + $delayedCompletionTime) / 2, 1).' min')
                ->description("Immediate: {$immediateCompletionTime}m | Delayed: {$delayedCompletionTime}m")
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),
        ];

        return $stats;
    }

    protected function getWeeklyTrend($sessions): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayAvg = $sessions
                ->filter(fn ($s) => $s->submitted_at->format('Y-m-d') === $date)
                ->avg('average_score');
            $days[] = $dayAvg ?? 0;
        }

        return $days;
    }
}
