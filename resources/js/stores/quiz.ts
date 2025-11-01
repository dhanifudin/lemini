import { defineStore } from 'pinia';
import { computed, reactive, ref } from 'vue';

export interface QuizSessionSummary {
    id: number;
    status: string;
    experiment_variant?: string | null;
    settings?: Record<string, unknown> | null;
    started_at?: string | null;
    submitted_at?: string | null;
}

export interface QuizSessionItemSummary {
    session_item_id: number;
    item_id: number;
    position: number;
    status: string;
    response: unknown;
    stem: string;
    type: string;
    options: Record<string, string> | string[] | null;
    meta?: Record<string, unknown> | null;
    objective_code: string;
    learning_objective?: string | null;
    score?: number | null;
    feedback?: Record<string, unknown> | null;
    flagged?: boolean;
    correct_answer?: string | null;
    rationale?: string | null;
}

export interface QuizSummary {
    average_score: number | null;
    correct: number;
    incorrect: number;
    pending_review: number;
    total_questions: number;
}

interface StartQuizPayload {
    size: number;
    objectives?: string[];
    timer_minutes?: number | null;
}

export const useQuizStore = defineStore('quiz', () => {
    const session = ref<QuizSessionSummary | null>(null);
    const items = ref<QuizSessionItemSummary[]>([]);
    const summary = ref<QuizSummary | null>(null);
    const currentIndex = ref(0);
    const answers = reactive<Record<number, unknown>>({});
    const flags = reactive<Record<number, boolean>>({});
    const loading = reactive({
        start: false,
        response: false,
        submit: false,
    });
    const error = ref<string | null>(null);

    const timeRemainingSeconds = ref<number | null>(null);
    let timerHandle: number | null = null;

    const currentItem = computed(() => items.value[currentIndex.value] ?? null);
    const hasSession = computed(() => session.value !== null);
    const timerActive = computed(() => timeRemainingSeconds.value !== null && session.value?.status !== 'submitted');
    const feedbackVariant = computed(() => (session.value?.settings as Record<string, unknown>)?.feedback_variant as string | undefined);
    const showImmediateFeedback = computed(() => feedbackVariant.value === 'immediate');
    const timeRemainingDisplay = computed(() => {
        if (timeRemainingSeconds.value === null) {
            return null;
        }

        const minutes = Math.max(0, Math.floor(timeRemainingSeconds.value / 60));
        const seconds = Math.max(0, timeRemainingSeconds.value % 60);

        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    });

    function extractResponseValue(response: unknown): unknown {
        if (response === null || response === undefined) {
            return null;
        }

        if (Array.isArray(response)) {
            return response;
        }

        if (typeof response === 'object' && 'value' in (response as Record<string, unknown>)) {
            return (response as Record<string, unknown>).value ?? null;
        }

        return response;
    }

    function clearTimer(): void {
        if (timerHandle !== null) {
            window.clearInterval(timerHandle);
            timerHandle = null;
        }

        timeRemainingSeconds.value = null;
    }

    function startTimer(seconds: number): void {
        clearTimer();

        timeRemainingSeconds.value = seconds;
        timerHandle = window.setInterval(() => {
            if (timeRemainingSeconds.value === null) {
                return;
            }

            if (timeRemainingSeconds.value <= 0) {
                clearTimer();
                if (session.value?.status !== 'submitted' && !loading.submit) {
                    logTimeExpired().catch(() => {
                        // Silently fail telemetry
                    });
                    submitQuiz().catch(() => {
                        // Errors surfaced via error ref in submitQuiz.
                    });
                }

                return;
            }

            timeRemainingSeconds.value -= 1;
        }, 1000);
    }

    async function startQuiz(payload: StartQuizPayload): Promise<void> {
        loading.start = true;
        error.value = null;

        try {
            const response = await fetch('/api/quizzes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                throw new Error(body.message ?? 'Unable to start quiz');
            }

            const body = (await response.json()) as {
                session: QuizSessionSummary;
                items: QuizSessionItemSummary[];
                summary?: QuizSummary;
            };

            session.value = body.session;
            items.value = body.items;
            summary.value = body.summary ?? null;
            currentIndex.value = 0;

            Object.keys(answers).forEach((key) => delete answers[Number(key)]);
            Object.keys(flags).forEach((key) => delete flags[Number(key)]);

            body.items.forEach((item) => {
                const value = extractResponseValue(item.response);
                if (value !== null && value !== undefined) {
                    answers[item.session_item_id] = value;
                }

                flags[item.session_item_id] = item.flagged ?? false;
            });

            const timerMinutes = (body.session.settings?.timer_minutes as number | undefined) ?? undefined;
            if (timerMinutes && timerMinutes > 0) {
                startTimer(timerMinutes * 60);
            } else {
                clearTimer();
            }
        } catch (err) {
            error.value = (err as Error).message;
            throw err;
        } finally {
            loading.start = false;
        }
    }

    async function saveResponse(sessionItemId: number, responseValue: unknown, flagged?: boolean): Promise<void> {
        if (!session.value) {
            return;
        }

        answers[sessionItemId] = responseValue;
        if (typeof flagged === 'boolean') {
            flags[sessionItemId] = flagged;
        }

        loading.response = true;
        error.value = null;

        try {
            const payload: Record<string, unknown> = {
                session_item_id: sessionItemId,
                response: responseValue,
            };

            if (typeof flagged === 'boolean') {
                payload.flagged = flagged;
            }

            const response = await fetch(`/api/quizzes/${session.value.id}/responses`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                throw new Error(body.message ?? 'Unable to save response');
            }

            const body = (await response.json()) as { item: QuizSessionItemSummary };
            const index = items.value.findIndex((item) => item.session_item_id === sessionItemId);

            if (index !== -1) {
                items.value[index] = body.item;
            }

            flags[sessionItemId] = body.item.flagged ?? flags[sessionItemId] ?? false;
        } catch (err) {
            error.value = (err as Error).message;
            throw err;
        } finally {
            loading.response = false;
        }
    }

    async function submitQuiz(): Promise<void> {
        if (!session.value) {
            return;
        }

        clearTimer();
        loading.submit = true;
        error.value = null;

        try {
            const response = await fetch(`/api/quizzes/${session.value.id}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
            });

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                throw new Error(body.message ?? 'Unable to submit quiz');
            }

            const body = (await response.json()) as {
                session: QuizSessionSummary;
                items: QuizSessionItemSummary[];
                summary?: QuizSummary;
            };

            session.value = body.session;
            items.value = body.items;
            summary.value = body.summary ?? summary.value;

            body.items.forEach((item) => {
                flags[item.session_item_id] = item.flagged ?? false;
            });
        } catch (err) {
            error.value = (err as Error).message;
            throw err;
        } finally {
            loading.submit = false;
        }
    }

    function goToIndex(index: number): void {
        if (index >= 0 && index < items.value.length) {
            currentIndex.value = index;
        }
    }

    function next(): void {
        goToIndex(currentIndex.value + 1);
    }

    function previous(): void {
        goToIndex(currentIndex.value - 1);
    }

    async function answerCurrent(responseValue: unknown): Promise<void> {
        const item = currentItem.value;
        if (!item) {
            return;
        }

        await saveResponse(item.session_item_id, responseValue, flags[item.session_item_id]);
    }

    async function toggleFlag(sessionItemId: number): Promise<void> {
        if (!session.value) {
            return;
        }

        const currentFlag = flags[sessionItemId] ?? false;
        const currentResponse = answers[sessionItemId] ?? null;

        await saveResponse(sessionItemId, currentResponse, !currentFlag);
    }

    function reset(): void {
        session.value = null;
        items.value = [];
        summary.value = null;
        currentIndex.value = 0;
        Object.keys(answers).forEach((key) => delete answers[Number(key)]);
        Object.keys(flags).forEach((key) => delete flags[Number(key)]);
        error.value = null;
        clearTimer();
    }

    async function logTelemetry(eventType: string, meta: Record<string, unknown> = {}): Promise<void> {
        if (!session.value) {
            return;
        }

        try {
            await fetch(`/api/quizzes/${session.value.id}/telemetry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify({
                    event_type: eventType,
                    meta,
                }),
            });
            // Fire and forget - don't block UI on telemetry failures
        } catch (err) {
            // Silently fail telemetry
            console.warn('Telemetry error:', err);
        }
    }

    async function logQuestionFlagged(sessionItemId: number): Promise<void> {
        await logTelemetry('quiz_question_flagged', { session_item_id: sessionItemId });
    }

    async function logQuestionSkipped(sessionItemId: number): Promise<void> {
        await logTelemetry('quiz_question_skipped', { session_item_id: sessionItemId });
    }

    async function logTimeExpired(): Promise<void> {
        await logTelemetry('quiz_time_expired', { remaining_questions: items.value.filter(i => i.status === 'pending').length });
    }

    async function logFeedbackViewed(): Promise<void> {
        await logTelemetry('quiz_feedback_viewed', { variant: feedbackVariant.value });
    }

    return {
        session,
        items,
        summary,
        currentIndex,
        currentItem,
        answers,
        flags,
        timeRemainingSeconds,
        timerActive,
        feedbackVariant,
        showImmediateFeedback,
        timeRemainingDisplay,
        loading,
        error,
        hasSession,
        startQuiz,
        saveResponse,
        answerCurrent,
        toggleFlag,
        submitQuiz,
        next,
        previous,
        goToIndex,
        reset,
        logTelemetry,
        logQuestionFlagged,
        logQuestionSkipped,
        logTimeExpired,
        logFeedbackViewed,
    };
});
