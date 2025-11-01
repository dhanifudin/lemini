<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Api\Concerns\FormatsQuizSession;
use App\Models\QuizSession;
use App\Models\QuizSessionItem;
use App\Services\Quiz\QuizSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizSessionResponseController extends Controller
{
    use FormatsQuizSession;

    public function __construct(
        protected QuizSessionService $service,
    ) {
    }

    public function store(Request $request, QuizSession $quizSession): JsonResponse
    {
        abort_unless($quizSession->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'session_item_id' => [
                'required',
                Rule::exists('quiz_session_items', 'id')->where(fn ($query) => $query->where('quiz_session_id', $quizSession->id)),
            ],
            'response' => ['nullable'],
            'flagged' => ['nullable', 'boolean'],
        ]);

        /** @var QuizSessionItem $sessionItem */
        $sessionItem = $quizSession->items()->whereKey($data['session_item_id'])->firstOrFail();

        $updated = $this->service->saveResponse($quizSession, $sessionItem, $data['response'] ?? null, $data['flagged'] ?? null);

        return response()->json([
            'item' => $this->formatQuizSessionItem($updated),
        ]);
    }
}
