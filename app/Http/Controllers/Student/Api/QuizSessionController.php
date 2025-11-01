<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Api\Concerns\FormatsQuizSession;
use App\Services\Quiz\QuizSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizSessionController extends Controller
{
    use FormatsQuizSession;

    public function __construct(
        protected QuizSessionService $service,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'size' => ['required', 'integer', 'min:1', 'max:20'],
            'objectives' => ['sometimes', 'array'],
            'objectives.*' => ['string'],
            'timer_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $session = $this->service->start($request->user(), $data);

        return response()->json($this->formatQuizSession($session));
    }
}
