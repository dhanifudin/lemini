<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class QuizExportController extends Controller
{
    public function export(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'objective' => ['nullable', 'string'],
            'variant' => ['nullable', 'string', 'in:immediate,delayed'],
        ]);

        $query = QuizSession::query()
            ->with(['user:id,first_name,last_name,email', 'items.item'])
            ->where('status', 'submitted');

        if (! empty($data['start_date'])) {
            $query->where('submitted_at', '>=', Carbon::parse($data['start_date']));
        }

        if (! empty($data['end_date'])) {
            $query->where('submitted_at', '<=', Carbon::parse($data['end_date']));
        }

        if (! empty($data['objective'])) {
            $query->whereHas('items.item', function ($q) use ($data) {
                $q->where('objective_code', $data['objective']);
            });
        }

        $sessions = $query->get();

        if (! empty($data['variant'])) {
            $sessions = $sessions->filter(fn ($s) => ($s->settings['feedback_variant'] ?? null) === $data['variant']);
        }

        $csv = $this->generateCsv($sessions);

        $filename = 'quiz-export-'.Carbon::now()->format('Y-m-d-His').'.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function generateCsv($sessions): string
    {
        $handle = fopen('php://temp', 'r+');

        // Header row
        fputcsv($handle, [
            'Session ID',
            'Student ID',
            'Student Name',
            'Student Email',
            'Experiment Variant',
            'Feedback Variant',
            'Objectives',
            'Total Questions',
            'Correct Count',
            'Incorrect Count',
            'Pending Review',
            'Average Score (%)',
            'Started At',
            'Submitted At',
            'Completion Time (minutes)',
        ]);

        // Data rows
        foreach ($sessions as $session) {
            $objectives = $session->items->pluck('item.objective_code')->unique()->implode(', ');
            $completionTime = $session->started_at && $session->submitted_at
                ? $session->started_at->diffInMinutes($session->submitted_at)
                : null;

            fputcsv($handle, [
                $session->id,
                $session->user_id,
                $session->user->first_name.' '.$session->user->last_name,
                $session->user->email,
                $session->experiment_variant,
                $session->settings['feedback_variant'] ?? 'N/A',
                $objectives,
                $session->items->count(),
                $session->correct_count,
                $session->incorrect_count,
                $session->pending_review_count,
                $session->average_score,
                $session->started_at?->toIso8601String(),
                $session->submitted_at?->toIso8601String(),
                $completionTime,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
