<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;

class ModerateFeedbackController extends Controller
{
    public function __invoke(Request $request, Feedback $feedback): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', new In(['approve', 'request_revision'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $teacher = $request->user();

        $feedback->loadMissing('attempt.item', 'attempt.user');

        if ($data['action'] === 'approve') {
            $feedback->status = 'published';
            $feedback->released_at = $feedback->released_at ?? now();

            if (! empty($data['notes'])) {
                $feedback->human_revision = $data['notes'];
            }
        }

        if ($data['action'] === 'request_revision') {
            $feedback->status = 'draft';
            $feedback->released_at = null;

            if (! empty($data['notes'])) {
                $feedback->human_revision = $data['notes'];
            }
        }

        $feedback->save();

        $audit = $feedback->audits()->create([
            'user_id' => $teacher->id,
            'action' => $data['action'],
            'notes' => $data['notes'] ?? null,
            'meta' => [
                'feedback_status' => $feedback->status,
            ],
        ]);

        return response()->json([
            'id' => $feedback->id,
            'status' => $feedback->status,
            'released_at' => optional($feedback->released_at)->toIso8601String(),
            'audit' => [
                'id' => $audit->id,
                'action' => $audit->action,
                'notes' => $audit->notes,
                'actor' => $teacher->name,
                'created_at' => optional($audit->created_at)->toIso8601String(),
            ],
        ]);
    }
}
