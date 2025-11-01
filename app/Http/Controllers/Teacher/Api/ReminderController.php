<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'feedback_ids' => ['required', 'array', 'max:50'],
            'feedback_ids.*' => ['integer', 'exists:feedback,id'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $teacher = $request->user();

        $feedbackRecords = Feedback::query()
            ->whereIn('id', $data['feedback_ids'])
            ->with(['audits', 'attempt.user'])
            ->get();

        foreach ($feedbackRecords as $feedback) {
            $feedback->audits()->create([
                'user_id' => $teacher->id,
                'action' => 'reminder_sent',
                'notes' => $data['message'] ?? null,
                'meta' => [
                    'recipient_student_id' => $feedback->attempt?->user_id,
                ],
            ]);
        }

        // Stub for actual email/notification dispatch.

        return response()->json([
            'count' => $feedbackRecords->count(),
        ]);
    }
}
