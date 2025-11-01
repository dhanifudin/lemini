<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Api\Concerns\FormatsQuizSession;
use App\Models\QuizSession;
use App\Services\Quiz\QuizSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizSessionSubmitController extends Controller
{
    use FormatsQuizSession;

    public function __construct(
        protected QuizSessionService $service,
    ) {
    }

    public function __invoke(Request $request, QuizSession $quizSession): JsonResponse
    {
        abort_unless($quizSession->user_id === $request->user()->id, 404);

        $session = $this->service->submit($quizSession);

        return response()->json($this->formatQuizSession($session));
    }
}
