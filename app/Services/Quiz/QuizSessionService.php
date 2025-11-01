<?php

namespace App\Services\Quiz;

use App\Models\AdaptiveRecommendationEvent;
use App\Models\QuizSession;
use App\Models\QuizSessionItem;
use App\Models\User;
use App\Services\Adaptive\AdaptivePracticeEngine;
use App\Services\Adaptive\MasteryScoringService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizSessionService
{
    public function __construct(
        protected AdaptivePracticeEngine $engine,
        protected MasteryScoringService $scoring,
    ) {
    }

    public function start(User $user, array $data): QuizSession
    {
        $size = max(1, min(20, (int) ($data['size'] ?? 5)));

        $objectiveCodes = collect($data['objectives'] ?? [])
            ->filter(fn ($code) => is_string($code) && $code !== '')
            ->unique()
            ->values()
            ->all();

        $bundle = $this->engine->bundle($user, $size, $objectiveCodes, true);
        $items = $bundle['items'];

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'objectives' => 'No quiz questions available for the selected options.',
            ]);
        }

        $settings = [
            'requested_size' => $size,
            'objectives' => $objectiveCodes,
        ];

        if (! empty($data['timer_minutes'])) {
            $settings['timer_minutes'] = (int) $data['timer_minutes'];
        }

        // Assign feedback timing variant for A/B testing
        // Variant A: immediate per-question feedback
        // Variant B: delayed feedback (summary-only)
        $feedbackVariant = $this->assignFeedbackVariant($user);
        $settings['feedback_variant'] = $feedbackVariant;

        return DB::transaction(function () use ($user, $settings, $items, $bundle, $feedbackVariant) {
            $session = QuizSession::create([
                'user_id' => $user->id,
                'experiment_variant' => $bundle['assignment']->variant,
                'status' => 'active',
                'settings' => $settings,
                'started_at' => now(),
            ]);

            foreach ($items as $index => $entry) {
                /** @var \App\Models\Item $item */
                $item = $entry['item'];

                QuizSessionItem::create([
                    'quiz_session_id' => $session->id,
                    'item_id' => $item->id,
                    'position' => $index + 1,
                    'status' => 'pending',
                ]);
            }

            AdaptiveRecommendationEvent::create([
                'user_id' => $user->id,
                'item_id' => null,
                'experiment_key' => $bundle['assignment']->experiment_key,
                'variant' => $bundle['assignment']->variant,
                'event_type' => 'quiz_started',
                'occurred_at' => now(),
                'meta' => [
                    'quiz_session_id' => $session->id,
                    'objective_codes' => $settings['objectives'],
                ],
            ]);

            return $session->load(['items.item.learningObjective', 'items.item.rubric']);
        });
    }

    public function saveResponse(QuizSession $session, QuizSessionItem $sessionItem, mixed $response, ?bool $flagged = null): QuizSessionItem
    {
        if ($session->status === 'submitted') {
            throw ValidationException::withMessages([
                'session' => 'Quiz has already been submitted.',
            ]);
        }

        $payload = $response;

        if ($payload !== null && ! is_array($payload)) {
            $payload = ['value' => $payload];
        }

        $sessionItem->fill([
            'response' => $payload,
            'status' => $payload === null ? 'pending' : 'answered',
        ]);

        if (! is_null($flagged)) {
            $sessionItem->flagged = $flagged;
        }

        $sessionItem->save();

        return $sessionItem->fresh()->load('item.learningObjective', 'item.rubric');
    }

    public function submit(QuizSession $session): QuizSession
    {
        if ($session->status === 'submitted') {
            return $session->fresh()->load(['items.item.learningObjective', 'items.item.rubric']);
        }

        [$finalSession, $objectiveScores] = DB::transaction(function () use ($session) {
            $session->loadMissing('items.item');

            $objectiveBuckets = [];
            $correct = 0;
            $incorrect = 0;
            $pendingReview = 0;
            $scoredSum = 0;
            $scoredCount = 0;

            foreach ($session->items as $sessionItem) {
                $content = $sessionItem->item;
                $responseValue = $this->extractResponseValue($sessionItem->response);
                $objectiveCode = $content->learningObjective->code ?? $content->objective_code;

                $feedback = [
                    'correct_answer' => $content->answer,
                    'explanation' => $content->rationale,
                ];

                if ($content->type === 'MCQ') {
                    $isCorrect = $responseValue !== null
                        && strcasecmp((string) $responseValue, (string) $content->answer) === 0;
                    $score = $isCorrect ? 100.0 : 0.0;

                    $sessionItem->fill([
                        'score' => $score,
                        'status' => $isCorrect ? 'correct' : 'incorrect',
                        'feedback' => $feedback,
                    ])->save();

                    $scoredSum += $score;
                    $scoredCount++;
                    $isCorrect ? $correct++ : $incorrect++;

                    $bucket = $objectiveBuckets[$objectiveCode] ?? ['sum' => 0.0, 'count' => 0];
                    $bucket['sum'] += $score;
                    $bucket['count']++;
                    $objectiveBuckets[$objectiveCode] = $bucket;
                } else {
                    $status = $responseValue === null ? 'pending' : 'pending_review';
                    if ($status === 'pending_review') {
                        $pendingReview++;
                    }

                    $sessionItem->fill([
                        'score' => null,
                        'status' => $status,
                        'feedback' => $feedback,
                    ])->save();
                }
            }

            $average = $scoredCount ? round($scoredSum / $scoredCount, 2) : null;

            $session->fill([
                'status' => 'submitted',
                'submitted_at' => now(),
                'average_score' => $average,
                'correct_count' => $correct,
                'incorrect_count' => $incorrect,
                'pending_review_count' => $pendingReview,
            ])->save();

            AdaptiveRecommendationEvent::create([
                'user_id' => $session->user_id,
                'item_id' => null,
                'experiment_key' => 'practice_engine_v1',
                'variant' => $session->experiment_variant,
                'event_type' => 'quiz_submitted',
                'occurred_at' => now(),
                'meta' => [
                    'quiz_session_id' => $session->id,
                    'average_score' => $average,
                    'correct' => $correct,
                    'incorrect' => $incorrect,
                    'pending_review' => $pendingReview,
                ],
            ]);

            return [
                $session->fresh()->load(['items.item.learningObjective', 'items.item.rubric', 'user']),
                $objectiveBuckets,
            ];
        });

        foreach ($objectiveScores as $objectiveCode => $bucket) {
            if (($bucket['count'] ?? 0) > 0) {
                $average = round($bucket['sum'] / $bucket['count'], 2);
                $this->scoring->updateFromQuiz($finalSession->user, (string) $objectiveCode, $average);
            }
        }

        return $finalSession;
    }

    protected function extractResponseValue(mixed $response): mixed
    {
        if (is_array($response) && array_key_exists('value', $response)) {
            return $response['value'];
        }

        return $response;
    }
    
    public function recalculateSummary(QuizSession $session): void
    {
        $session->loadMissing('items');
        
        $scored = $session->items->whereNotNull('score');
        $totalScore = $scored->sum('score');
        $count = $scored->count();
        $average = $count ? round($totalScore / $count, 2) : null;

        $correct = $session->items->where('status', 'correct')->count();
        $incorrect = $session->items->where('status', 'incorrect')->count();
        $pending = $session->items->where('status', 'pending_review')->count();

        $session->update([
            'average_score' => $average,
            'correct_count' => $correct,
            'incorrect_count' => $incorrect,
            'pending_review_count' => $pending,
        ]);
    }

    public function logReviewedEvent(QuizSession $session, User $teacher): void
    {
        AdaptiveRecommendationEvent::create([
            'user_id' => $session->user_id,
            'item_id' => null,
            'experiment_key' => 'practice_engine_v1',
            'variant' => $session->experiment_variant,
            'event_type' => 'quiz_reviewed',
            'occurred_at' => now(),
            'meta' => [
                'quiz_session_id' => $session->id,
                'teacher_id' => $teacher->id,
                'average_score' => $session->average_score,
            ],
        ]);
    }

    /**
     * Assign feedback timing variant for A/B testing
     * Variant A: immediate per-question feedback
     * Variant B: delayed feedback (summary-only)
     */
    protected function assignFeedbackVariant(User $user): string
    {
        // Simple assignment based on user ID (50/50 split)
        // In production, this could use a more sophisticated experiment framework
        return ($user->id % 2 === 0) ? 'immediate' : 'delayed';
    }

    /**
     * Log telemetry events for quiz interactions
     */
    public function logTelemetryEvent(QuizSession $session, string $eventType, array $meta = []): void
    {
        AdaptiveRecommendationEvent::create([
            'user_id' => $session->user_id,
            'item_id' => $meta['item_id'] ?? null,
            'experiment_key' => 'practice_engine_v1',
            'variant' => $session->experiment_variant,
            'event_type' => $eventType,
            'occurred_at' => now(),
            'meta' => array_merge([
                'quiz_session_id' => $session->id,
                'feedback_variant' => $session->settings['feedback_variant'] ?? null,
            ], $meta),
        ]);
    }
}
