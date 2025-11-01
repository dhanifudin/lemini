<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use App\Services\Quiz\QuizSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizTelemetryController extends Controller
{
    public function __construct(
        protected QuizSessionService $service,
    ) {
    }

    public function store(Request $request, QuizSession $quizSession): JsonResponse
    {
        abort_unless($quizSession->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'event_type' => [
                'required',
                'string',
                'in:quiz_question_flagged,quiz_question_skipped,quiz_time_expired,quiz_feedback_viewed',
            ],
            'meta' => ['nullable', 'array'],
        ]);

        $this->service->logTelemetryEvent(
            $quizSession,
            $data['event_type'],
            $data['meta'] ?? []
        );

        return response()->json(['success' => true]);
    }
}
