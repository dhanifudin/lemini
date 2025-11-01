<?php

namespace App\Http\Controllers\Student\Api\Concerns;

use App\Models\QuizSession;
use App\Models\QuizSessionItem;
use Illuminate\Support\Collection;

trait FormatsQuizSession
{
    protected function formatQuizSession(QuizSession $session): array
    {
        $session->loadMissing(['items.item.learningObjective', 'items.item.rubric']);

        $formattedItems = $session->items
            ->sortBy('position')
            ->values()
            ->map(fn (QuizSessionItem $item) => $this->formatQuizSessionItem($item));

        return [
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'experiment_variant' => $session->experiment_variant,
                'settings' => $session->settings,
                'started_at' => optional($session->started_at)->toIso8601String(),
                'submitted_at' => optional($session->submitted_at)->toIso8601String(),
            ],
            'items' => $formattedItems->all(),
            'summary' => $this->formatSummary($session, $formattedItems),
        ];
    }

    protected function formatQuizSessionItem(QuizSessionItem $item): array
    {
        $item->loadMissing('item.learningObjective', 'item.rubric');

        $content = $item->item;

        $feedback = $item->feedback ?? [];

        return [
            'session_item_id' => $item->id,
            'item_id' => $item->item_id,
            'position' => $item->position,
            'status' => $item->status,
            'response' => $item->response,
            'stem' => $content->stem,
            'type' => $content->type,
            'options' => $content->options,
            'meta' => $content->meta,
            'objective_code' => $content->learningObjective->code ?? $content->objective_code,
            'learning_objective' => $content->learningObjective->title ?? null,
            'score' => $item->score,
            'feedback' => $feedback,
            'flagged' => $item->flagged,
            'correct_answer' => $feedback['correct_answer'] ?? $content->answer,
            'rationale' => $feedback['explanation'] ?? $content->rationale,
        ];
    }

    protected function formatSummary(QuizSession $session, Collection $items): array
    {
        $scored = $session->items->whereNotNull('score');
        $totalScore = $scored->sum('score');
        $count = $scored->count();
        $average = $count ? round($totalScore / $count, 1) : null;

        $correct = $scored->where('status', 'correct')->count();
        $incorrect = $scored->where('status', 'incorrect')->count();
        $pending = $session->items->where('status', 'pending_review')->count();

        return [
            'average_score' => $average,
            'correct' => $correct,
            'incorrect' => $incorrect,
            'pending_review' => $pending,
            'total_questions' => $items->count(),
        ];
    }
}
