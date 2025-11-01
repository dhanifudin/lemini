<?php

namespace App\Console\Commands;

use App\Models\QuizSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateQuizMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:aggregate-metrics {--days=7 : Number of days to aggregate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate quiz performance metrics by objective and variant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $startDate = Carbon::now()->subDays($days);

        $this->info("Aggregating quiz metrics for the last {$days} days...");

        // Aggregate by objective
        $objectiveMetrics = DB::table('quiz_session_items')
            ->join('quiz_sessions', 'quiz_session_items.quiz_session_id', '=', 'quiz_sessions.id')
            ->join('items', 'quiz_session_items.item_id', '=', 'items.id')
            ->where('quiz_sessions.status', 'submitted')
            ->where('quiz_sessions.submitted_at', '>=', $startDate)
            ->whereNotNull('quiz_session_items.score')
            ->select(
                'items.objective_code',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('AVG(quiz_session_items.score) as avg_score'),
                DB::raw('MIN(quiz_session_items.score) as min_score'),
                DB::raw('MAX(quiz_session_items.score) as max_score'),
                DB::raw('SUM(CASE WHEN quiz_session_items.status = "correct" THEN 1 ELSE 0 END) as correct_count')
            )
            ->groupBy('items.objective_code')
            ->orderBy('avg_score', 'asc')
            ->get();

        $this->table(
            ['Objective', 'Attempts', 'Avg Score', 'Min', 'Max', 'Correct Count'],
            $objectiveMetrics->map(fn ($row) => [
                $row->objective_code,
                $row->total_attempts,
                round($row->avg_score, 2).'%',
                round($row->min_score, 2),
                round($row->max_score, 2),
                $row->correct_count,
            ])
        );

        // Aggregate by feedback variant
        $variantMetrics = QuizSession::query()
            ->where('status', 'submitted')
            ->where('submitted_at', '>=', $startDate)
            ->whereNotNull('average_score')
            ->get()
            ->groupBy(fn ($session) => $session->settings['feedback_variant'] ?? 'unknown')
            ->map(function ($sessions, $variant) {
                return [
                    'variant' => $variant,
                    'sessions' => $sessions->count(),
                    'avg_score' => round($sessions->avg('average_score'), 2),
                    'completion_time_avg' => round($sessions->map(fn ($s) => $s->started_at->diffInMinutes($s->submitted_at))->avg(), 2),
                ];
            });

        $this->info("\nPerformance by Feedback Variant:");
        $this->table(
            ['Variant', 'Sessions', 'Avg Score', 'Avg Completion Time (min)'],
            $variantMetrics->map(fn ($row) => [
                $row['variant'],
                $row['sessions'],
                $row['avg_score'].'%',
                $row['completion_time_avg'],
            ])
        );

        // Telemetry event summary
        $eventCounts = DB::table('adaptive_recommendation_events')
            ->whereIn('event_type', [
                'quiz_question_flagged',
                'quiz_question_skipped',
                'quiz_time_expired',
                'quiz_feedback_viewed',
            ])
            ->where('occurred_at', '>=', $startDate)
            ->select('event_type', DB::raw('COUNT(*) as count'))
            ->groupBy('event_type')
            ->get();

        $this->info("\nTelemetry Events:");
        $this->table(
            ['Event Type', 'Count'],
            $eventCounts->map(fn ($row) => [$row->event_type, $row->count])
        );

        $this->info("\n✓ Aggregation complete!");

        return Command::SUCCESS;
    }
}
