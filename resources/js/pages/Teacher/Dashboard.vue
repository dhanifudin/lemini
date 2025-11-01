<script setup lang="ts">
import SnapshotCard from '@/components/teacher/SnapshotCard.vue';
import PendingFeedbackTable from '@/components/teacher/PendingFeedbackTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTeacherStore } from '@/stores/teacher';
import type { AppPageProps, BreadcrumbItemType } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, reactive } from 'vue';

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const page = usePage<AppPageProps>();
const store = useTeacherStore();

const selection = reactive<Set<number>>(new Set());

const summary = computed(() => store.summary);
const snapshot = computed(() => summary.value?.snapshot);
const masteryPreview = computed(() => summary.value?.mastery ?? []);
const pendingPreview = computed(() => summary.value?.pending_feedback_preview ?? []);
const pendingFeedback = computed(() => store.pendingFeedback);

const teacherName = computed(() => page.props.auth.user.name);

function toggleSelection(id: number): void {
    if (selection.has(id)) {
        selection.delete(id);
    } else {
        selection.add(id);
    }
}

function clearSelection(): void {
    selection.clear();
}

async function approveFeedback(id: number): Promise<void> {
    await store.moderateFeedback(id, { action: 'approve' });
    selection.delete(id);
}

async function requestRevision(id: number): Promise<void> {
    await store.moderateFeedback(id, { action: 'request_revision' });
    selection.delete(id);
}

async function sendReminders(): Promise<void> {
    const ids = Array.from(selection);
    if (!ids.length) {
        return;
    }

    await store.sendReminders(ids);
    clearSelection();
}

onMounted(async () => {
    await Promise.all([store.fetchSummary(), store.fetchPending()]);
});
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <section class="flex flex-col gap-6 px-4 pb-8 pt-4 lg:px-6">
            <header class="space-y-1">
                <h1 class="text-2xl font-semibold text-foreground">Welcome back, {{ teacherName }}</h1>
                <p class="text-sm text-muted-foreground">
                    Monitor classroom performance, review AI feedback, and keep students on track.
                </p>
            </header>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <SnapshotCard
                    title="Attempts Today"
                    :value="snapshot?.attempts_today ?? '—'"
                    description="Student submissions logged today"
                />
                <SnapshotCard
                    title="Attempts (7 days)"
                    :value="snapshot?.attempts_last_7_days ?? '—'"
                    description="Total attempts this week"
                />
                <SnapshotCard
                    title="Pending Feedback"
                    :value="snapshot?.pending_feedback ?? '—'"
                    description="Awaiting teacher approval"
                />
                <SnapshotCard
                    title="Published (7 days)"
                    :value="snapshot?.feedback_published_last_7_days ?? '—'"
                    description="Feedback released to students"
                />
            </div>

            <div class="grid gap-4 lg:grid-cols-5">
                <section id="queue" class="lg:col-span-3 space-y-4">
                    <h2 class="text-lg font-semibold text-foreground">Feedback moderation queue</h2>
                    <PendingFeedbackTable
                        :items="pendingFeedback"
                        :loading="store.loading.queue"
                        :selected-ids="Array.from(selection)"
                        :moderation-busy="store.loading.moderation"
                        :reminder-busy="store.loading.reminder"
                        @toggle="toggleSelection"
                        @approve="approveFeedback"
                        @revise="requestRevision"
                        @remind="sendReminders"
                    />
                </section>

                <aside class="lg:col-span-2 space-y-4">
                    <section class="rounded-xl border border-border bg-background/60 p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-foreground">Mastery highlights</h2>
                        <ul class="mt-4 space-y-3 text-sm">
                            <li v-for="item in masteryPreview" :key="item.objective_code" class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-medium text-foreground">{{ item.objective_code }}</p>
                                    <p class="text-xs text-muted-foreground">{{ item.samples }} samples</p>
                                </div>
                                <span class="text-sm font-semibold">{{ item.average_percent ?? '—' }}%</span>
                            </li>
                            <li v-if="!masteryPreview.length" class="text-sm text-muted-foreground">
                                Once students submit attempts, mastery insights will appear here.
                            </li>
                        </ul>
                    </section>

                    <section class="rounded-xl border border-border bg-background/60 p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-foreground">Awaiting review</h2>
                        <ul class="mt-3 space-y-3 text-sm">
                            <li v-for="item in pendingPreview" :key="item.id" class="rounded-lg border border-border/70 p-3">
                                <p class="font-medium text-foreground">{{ item.student }}</p>
                                <p class="text-xs text-muted-foreground">{{ item.objective_code ?? '—' }}</p>
                                <p class="text-[11px] uppercase tracking-wide text-muted-foreground">
                                    Submitted {{ item.submitted_at ? new Date(item.submitted_at).toLocaleString() : '—' }}
                                </p>
                            </li>
                            <li v-if="!pendingPreview.length" class="text-sm text-muted-foreground">
                                No new submissions waiting for review.
                            </li>
                        </ul>
                    </section>
                </aside>
            </div>
        </section>
    </AppLayout>
</template>
