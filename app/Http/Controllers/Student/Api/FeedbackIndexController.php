<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeedbackIndexController extends Controller
{
    public function __invoke(Request $request): array
    {
        $student = $request->user();

        $feedback = Feedback::query()
            ->whereHas('attempt', fn ($query) => $query->where('user_id', $student->id))
            ->with(['attempt.item.rubric'])
            ->latest('created_at')
            ->get()
            ->map(fn (Feedback $feedback) => [
                'id' => $feedback->id,
                'status' => $feedback->status,
                'summary' => $feedback->ai_text['summary'] ?? null,
                'action_steps' => $feedback->ai_text['action_steps'] ?? [],
                'strengths' => $feedback->ai_text['strengths'] ?? null,
                'human_revision' => $feedback->human_revision,
                'released_at' => optional($feedback->released_at)->toIso8601String(),
                'created_at' => optional($feedback->created_at)->toIso8601String(),
                'attempt' => [
                    'id' => $feedback->attempt->id,
                    'score' => $feedback->attempt->score,
                    'submitted_at' => optional($feedback->attempt->created_at)->toIso8601String(),
                    'response_preview' => Str::limit($feedback->attempt->response, 140),
                    'response' => $feedback->attempt->response,
                ],
                'item' => [
                    'id' => $feedback->attempt->item?->id,
                    'objective_code' => $feedback->attempt->item?->objective_code,
                    'stem' => $feedback->attempt->item?->stem,
                    'type' => $feedback->attempt->item?->type,
                    'rubric' => [
                        'name' => $feedback->attempt->item?->rubric?->name,
                    ],
                ],
            ]);

        return [
            'published' => $feedback->where('status', 'published')->values(),
            'draft' => $feedback->where('status', '!=', 'published')->values(),
        ];
    }
}
