import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

interface MasterySummary {
    objective_code: string;
    level: string;
    progress_percent: number;
    last_seen_at?: string | null;
}

interface RecommendationPayload {
    focus_area?: string;
    suggested_resource?: string;
    next_check_in?: string;
}

interface Recommendation {
    id: number;
    payload: RecommendationPayload;
    chosen: boolean;
}

interface DashboardResponse {
    mastery: MasterySummary[];
    featuredRecommendation: Recommendation | null;
    activity: Array<{
        type: string;
        title: string;
        subtitle?: string | null;
        score?: number | null;
        occurred_at?: string | null;
    }>;
}

interface FeedbackResponse {
    published: FeedbackRecord[];
    draft: FeedbackRecord[];
}

export interface FeedbackRecord {
    id: number;
    status: string;
    summary?: string | null;
    action_steps?: string[];
    strengths?: string | null;
    human_revision?: string | null;
    released_at?: string | null;
    created_at?: string | null;
    attempt: {
        id: number;
        score: number | null;
        submitted_at?: string | null;
        response_preview?: string | null;
        response?: string | null;
    };
    item: {
        id: number | null;
        objective_code?: string | null;
        stem?: string | null;
        type?: string | null;
        rubric?: {
            name?: string | null;
        } | null;
    };
}

interface PracticeResponse {
    queue: PracticeItem[];
    resources: Array<{
        title: string;
        type: string;
        topic: string;
        url: string;
    }>;
}

export interface PracticeItem {
    id: number;
    objective_code: string;
    stem: string;
    type: string;
    rubric?: {
        name?: string | null;
        criteria?: Record<string, string> | null;
        levels?: Record<string, string> | null;
    } | null;
    meta?: Record<string, unknown> | null;
    priority: number;
    recommended_level: string;
    reason?: string;
}

const TTL_MS = 1000 * 60 * 5;

export const useStudentStore = defineStore('student-hub', () => {
    const dashboard = ref<DashboardResponse | null>(null);
    const feedback = ref<FeedbackResponse | null>(null);
    const practice = ref<PracticeResponse | null>(null);
    const errors = ref<string | null>(null);

    const lastFetched = ref({
        dashboard: 0,
        feedback: 0,
        practice: 0,
    });

    const loading = ref({
        dashboard: false,
        feedback: false,
        practice: false,
        reflection: false,
    });

    const publishedFeedback = computed(() => feedback.value?.published ?? []);
    const draftFeedback = computed(() => feedback.value?.draft ?? []);

    async function fetchDashboard(force = false): Promise<void> {
        if (!force && Date.now() - lastFetched.value.dashboard < TTL_MS) {
            return;
        }

        loading.value.dashboard = true;
        errors.value = null;

        try {
            const response = await fetch('/api/dashboard', {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load dashboard data');
            }

            dashboard.value = (await response.json()) as DashboardResponse;
            lastFetched.value.dashboard = Date.now();
        } catch (error) {
            errors.value = (error as Error).message;
        } finally {
            loading.value.dashboard = false;
        }
    }

    async function fetchFeedback(force = false): Promise<void> {
        if (!force && Date.now() - lastFetched.value.feedback < TTL_MS) {
            return;
        }

        loading.value.feedback = true;
        errors.value = null;

        try {
            const response = await fetch('/api/feedback', {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load feedback records');
            }

            feedback.value = (await response.json()) as FeedbackResponse;
            lastFetched.value.feedback = Date.now();
        } catch (error) {
            errors.value = (error as Error).message;
        } finally {
            loading.value.feedback = false;
        }
    }

    async function fetchPractice(force = false): Promise<void> {
        if (!force && Date.now() - lastFetched.value.practice < TTL_MS) {
            return;
        }

        loading.value.practice = true;
        errors.value = null;

        try {
            const response = await fetch('/api/practice-items', {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Unable to load practice queue');
            }

            practice.value = (await response.json()) as PracticeResponse;
            lastFetched.value.practice = Date.now();
        } catch (error) {
            errors.value = (error as Error).message;
        } finally {
            loading.value.practice = false;
        }
    }

    async function submitReflection(feedbackId: number, note: string): Promise<void> {
        loading.value.reflection = true;
        errors.value = null;

        try {
            const response = await fetch(`/api/feedback/${feedbackId}/reflect`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                },
                body: JSON.stringify({ note }),
            });

            if (!response.ok) {
                throw new Error('Unable to save reflection');
            }
        } catch (error) {
            errors.value = (error as Error).message;
            throw error;
        } finally {
            loading.value.reflection = false;
        }
    }

    function resetErrors(): void {
        errors.value = null;
    }

    return {
        dashboard,
        feedback,
        practice,
        loading,
        errors,
        publishedFeedback,
        draftFeedback,
        fetchDashboard,
        fetchFeedback,
        fetchPractice,
        submitReflection,
        resetErrors,
    };
});
