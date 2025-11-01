<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use App\Models\QuizReview;
use App\Services\Quiz\QuizSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizSessionController extends Controller
{
    public function __construct(
        protected QuizSessionService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'in:active,submitted'],
            'objective' => ['nullable', 'string'],
            'student_id' => ['nullable', 'integer', 'exists:users,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = QuizSession::query()
            ->with([
                'user:id,first_name,last_name,email',
                'items.item.learningObjective',
                'review',
            ])
            ->latest('submitted_at');

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (! empty($data['objective'])) {
            $query->whereHas('items.item', function ($q) use ($data) {
                $q->where('objective_code', $data['objective']);
            });
        }

        if (! empty($data['student_id'])) {
            $query->where('user_id', $data['student_id']);
        }

        $sessions = $query->paginate($data['per_page'] ?? 20);

        return response()->json([
            'sessions' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'student' => [
                        'id' => $session->user->id,
                        'name' => $session->user->first_name.' '.$session->user->last_name,
                        'email' => $session->user->email,
                    ],
                    'status' => $session->status,
                    'average_score' => $session->average_score,
                    'correct_count' => $session->correct_count,
                    'incorrect_count' => $session->incorrect_count,
                    'pending_review_count' => $session->pending_review_count,
                    'started_at' => $session->started_at?->toIso8601String(),
                    'submitted_at' => $session->submitted_at?->toIso8601String(),
                    'objectives' => $session->items->pluck('item.objective_code')->unique()->values(),
                    'review' => $session->review ? [
                        'id' => $session->review->id,
                        'status' => $session->review->status,
                        'reviewed_at' => $session->review->reviewed_at?->toIso8601String(),
                    ] : null,
                ];
            }),
            'meta' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'per_page' => $sessions->perPage(),
                'total' => $sessions->total(),
            ],
        ]);
    }

    public function show(QuizSession $quizSession): JsonResponse
    {
        $quizSession->loadMissing([
            'user:id,first_name,last_name,email',
            'items.item.learningObjective',
            'items.item.rubric',
            'review.teacher',
        ]);

        return response()->json([
            'session' => [
                'id' => $quizSession->id,
                'student' => [
                    'id' => $quizSession->user->id,
                    'name' => $quizSession->user->first_name.' '.$quizSession->user->last_name,
                    'email' => $quizSession->user->email,
                ],
                'status' => $quizSession->status,
                'experiment_variant' => $quizSession->experiment_variant,
                'settings' => $quizSession->settings,
                'average_score' => $quizSession->average_score,
                'correct_count' => $quizSession->correct_count,
                'incorrect_count' => $quizSession->incorrect_count,
                'pending_review_count' => $quizSession->pending_review_count,
                'started_at' => $quizSession->started_at?->toIso8601String(),
                'submitted_at' => $quizSession->submitted_at?->toIso8601String(),
            ],
            'items' => $quizSession->items->sortBy('position')->map(function ($item) {
                return [
                    'id' => $item->id,
                    'position' => $item->position,
                    'status' => $item->status,
                    'response' => $item->response,
                    'score' => $item->score,
                    'feedback' => $item->feedback,
                    'flagged' => $item->flagged,
                    'item' => [
                        'id' => $item->item->id,
                        'stem' => $item->item->stem,
                        'type' => $item->item->type,
                        'options' => $item->item->options,
                        'answer' => $item->item->answer,
                        'rationale' => $item->item->rationale,
                        'objective_code' => $item->item->objective_code,
                        'learning_objective' => $item->item->learningObjective?->title,
                    ],
                ];
            })->values(),
            'review' => $quizSession->review ? [
                'id' => $quizSession->review->id,
                'status' => $quizSession->review->status,
                'notes' => $quizSession->review->notes,
                'reviewed_at' => $quizSession->review->reviewed_at?->toIso8601String(),
                'teacher' => $quizSession->review->teacher ? [
                    'id' => $quizSession->review->teacher->id,
                    'name' => $quizSession->review->teacher->first_name.' '.$quizSession->review->teacher->last_name,
                ] : null,
            ] : null,
        ]);
    }

    public function review(Request $request, QuizSession $quizSession): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,reviewed,approved'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'item_reviews' => ['nullable', 'array'],
            'item_reviews.*.session_item_id' => ['required', 'integer', 'exists:quiz_session_items,id'],
            'item_reviews.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'item_reviews.*.feedback_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($request, $quizSession, $data) {
            // Create or update review record
            $review = QuizReview::updateOrCreate(
                ['quiz_session_id' => $quizSession->id],
                [
                    'teacher_id' => $request->user()->id,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                    'reviewed_at' => in_array($data['status'], ['reviewed', 'approved']) ? now() : null,
                ]
            );

            // Process item-level reviews (SAQ scoring)
            if (! empty($data['item_reviews'])) {
                foreach ($data['item_reviews'] as $itemReview) {
                    $sessionItem = $quizSession->items()
                        ->where('id', $itemReview['session_item_id'])
                        ->first();

                    if ($sessionItem && $sessionItem->status === 'pending_review') {
                        $feedback = $sessionItem->feedback ?? [];

                        if (isset($itemReview['score'])) {
                            $sessionItem->score = $itemReview['score'];
                            $sessionItem->status = $itemReview['score'] >= 70 ? 'correct' : 'incorrect';
                        }

                        if (! empty($itemReview['feedback_notes'])) {
                            $feedback['teacher_notes'] = $itemReview['feedback_notes'];
                        }

                        $sessionItem->feedback = $feedback;
                        $sessionItem->save();
                    }
                }

                // Recalculate session summary
                $this->service->recalculateSummary($quizSession);
            }

            // Log adaptive event
            $this->service->logReviewedEvent($quizSession, $request->user());

            return $review->fresh(['teacher', 'session']);
        });

        return response()->json([
            'review' => [
                'id' => $result->id,
                'status' => $result->status,
                'notes' => $result->notes,
                'reviewed_at' => $result->reviewed_at?->toIso8601String(),
                'teacher' => [
                    'id' => $result->teacher->id,
                    'name' => $result->teacher->first_name.' '.$result->teacher->last_name,
                ],
            ],
        ]);
    }
}
