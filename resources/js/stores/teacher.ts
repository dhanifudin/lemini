import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

interface SnapshotMetrics {
    attempts_today: number;
    attempts_last_7_days: number;
    students_total: number;
    pending_feedback: number;
    feedback_published_last_7_days: number;
}

interface MasteryPreview {
    objective_code: string;
    average_percent: number | null;
    level_label: string;
    samples: number;
}

export interface PendingFeedbackItem {
    id: number;
    status: string;
    student: {
        id: number | null;
        name: string | null;
    };
    attempt: {
        id: number | null;
        submitted_at: string | null;
        score: number | null;
        response_preview: string;
    };
    item: {
        objective_code: string | null;
        stem: string | null;
        rubric: string | null;
    };
    needs_reflection: boolean;
    last_action: {
        action: string;
        by: string | null;
        at: string | null;
    } | null;
    created_at: string | null;
}

interface SummaryResponse {
    snapshot: SnapshotMetrics;
    mastery: MasteryPreview[];
    pending_feedback_preview: Array<{
        id: number;
        student: string;
        objective_code: string | null;
        submitted_at: string | null;
        status: string;
    }>;
    generated_at: string;
}

export const useTeacherStore = defineStore('teacher-center', () => {
    const summary = ref<SummaryResponse | null>(null);
    const pendingFeedback = ref<PendingFeedbackItem[]>([]);
    const loading = ref({
        summary: false,
        queue: false,
        moderation: false,
        reminder: false,
    });
    const error = ref<string | null>(null);

    const hasPending = computed(() => pendingFeedback.value.length > 0);

    async function fetchSummary(): Promise<void> {
        loading.value.summary = true;
        error.value = null;

        try {
            const response = await fetch('/teacher/api/dashboard', {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load teacher dashboard metrics');
            }

            summary.value = (await response.json()) as SummaryResponse;
        } catch (err) {
            error.value = (err as Error).message;
        } finally {
            loading.value.summary = false;
        }
    }

    async function fetchPending(limit = 25): Promise<void> {
        loading.value.queue = true;
        error.value = null;

        try {
            const response = await fetch(`/teacher/api/pending-feedback?limit=${limit}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load pending feedback');
            }

            const body = (await response.json()) as { data: PendingFeedbackItem[] };
            pendingFeedback.value = body.data;
        } catch (err) {
            error.value = (err as Error).message;
        } finally {
            loading.value.queue = false;
        }
    }

    async function moderateFeedback(feedbackId: number, payload: { action: 'approve' | 'request_revision'; notes?: string }): Promise<void> {
        loading.value.moderation = true;
        error.value = null;

        try {
            const response = await fetch(`/teacher/api/feedback/${feedbackId}/moderate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Unable to update feedback status');
            }

            pendingFeedback.value = pendingFeedback.value.filter((item) => item.id !== feedbackId);
            await fetchSummary();
        } catch (err) {
            error.value = (err as Error).message;
            throw err;
        } finally {
            loading.value.moderation = false;
        }
    }

    async function sendReminders(feedbackIds: number[], message?: string): Promise<void> {
        loading.value.reminder = true;
        error.value = null;

        try {
            const response = await fetch('/teacher/api/reminders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify({ feedback_ids: feedbackIds, message }),
            });

            if (!response.ok) {
                throw new Error('Unable to send reminders');
            }
        } catch (err) {
            error.value = (err as Error).message;
            throw err;
        } finally {
            loading.value.reminder = false;
        }
    }

    function resetError(): void {
        error.value = null;
    }

    return {
        summary,
        pendingFeedback,
        loading,
        error,
        hasPending,
        fetchSummary,
        fetchPending,
        moderateFeedback,
        sendReminders,
        resetError,
    };
});
