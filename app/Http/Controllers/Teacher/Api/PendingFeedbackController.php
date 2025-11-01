<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class PendingFeedbackController extends Controller
{
    public function __invoke(Request $request): array
    {
        $limit = (int) $request->integer('limit', 25);

        $pending = Feedback::query()
            ->with([
                'attempt.user',
                'attempt.item.rubric',
                'reflections',
                'audits' => function ($query): void {
                    $query->latest();
                },
            ])
            ->where('status', '!=', 'published')
            ->orderBy('created_at')
            ->limit($limit)
            ->get()
            ->map(function (Feedback $feedback) {
                $latestAudit = $feedback->audits->first();

                return [
                    'id' => $feedback->id,
                    'status' => $feedback->status,
                    'student' => [
                        'id' => $feedback->attempt?->user?->id,
                        'name' => $feedback->attempt?->user?->name,
                    ],
                    'attempt' => [
                        'id' => $feedback->attempt?->id,
                        'submitted_at' => optional($feedback->attempt?->created_at)->toIso8601String(),
                        'score' => $feedback->attempt?->score,
                        'response_preview' => str($feedback->attempt?->response)->limit(160)->toString(),
                    ],
                    'item' => [
                        'objective_code' => $feedback->attempt?->item?->objective_code,
                        'stem' => $feedback->attempt?->item?->stem,
                        'rubric' => $feedback->attempt?->item?->rubric?->name,
                    ],
                    'needs_reflection' => $feedback->reflections->isEmpty(),
                    'last_action' => $latestAudit ? [
                        'action' => $latestAudit->action,
                        'by' => $latestAudit->user?->name,
                        'at' => optional($latestAudit->created_at)->toIso8601String(),
                    ] : null,
                    'created_at' => optional($feedback->created_at)->toIso8601String(),
                ];
            });

        return [
            'data' => $pending,
        ];
    }
}
