<script setup lang="ts">
import RecommendationHighlight from '@/components/student/RecommendationHighlight.vue';
import MasteryProgressCard from '@/components/student/MasteryProgressCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useStudentStore } from '@/stores/student';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const store = useStudentStore();

const dashboard = computed(() => store.dashboard);
const activity = computed(() => dashboard.value?.activity ?? []);
const mastery = computed(() => dashboard.value?.mastery ?? []);

const isLoading = computed(() => store.loading.dashboard && !dashboard.value);

onMounted(async () => {
    await store.fetchDashboard();
});
</script>

<template>
    <Head title="Student Dashboard" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <section class="flex flex-col gap-6 px-4 pb-8 pt-4 lg:px-6">
            <RecommendationHighlight
                :payload="dashboard?.featuredRecommendation?.payload"
                primary-path="/practice"
            />

            <div class="space-y-3">
                <header class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-foreground">Mastery snapshot</h2>
                    <span class="text-xs text-muted-foreground">
                        Updated automatically after each attempt
                    </span>
                </header>
                <div
                    class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <MasteryProgressCard
                        v-for="record in mastery"
                        :key="record.objective_code"
                        :objective-code="record.objective_code"
                        :level="record.level"
                        :progress-percent="record.progress_percent"
                        :last-seen-at="record.last_seen_at"
                    />
                    <div
                        v-if="!isLoading && mastery.length === 0"
                        class="rounded-xl border border-dashed border-border p-6 text-sm text-muted-foreground"
                    >
                        Complete your first biology practice set to unlock mastery tracking.
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <header class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-foreground">Recent activity</h2>
                </header>

                <div v-if="isLoading" class="grid gap-3">
                    <div class="h-16 animate-pulse rounded-xl bg-muted/40" />
                    <div class="h-16 animate-pulse rounded-xl bg-muted/40" />
                    <div class="h-16 animate-pulse rounded-xl bg-muted/40" />
                </div>
                <ul
                    v-else-if="activity.length"
                    class="grid gap-3"
                >
                    <li
                        v-for="item in activity"
                        :key="`${item.type}-${item.occurred_at}-${item.title}`"
                        class="flex flex-col gap-2 rounded-xl border border-border bg-background/60 p-4 text-sm shadow-sm"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-medium text-foreground">{{ item.title }}</span>
                            <span class="text-xs text-muted-foreground">
                                {{ item.occurred_at ? new Date(item.occurred_at).toLocaleString() : 'Pending' }}
                            </span>
                        </div>
                        <p v-if="item.subtitle" class="text-sm text-muted-foreground">
                            {{ item.subtitle }}
                        </p>
                        <p v-if="item.score !== undefined" class="text-xs text-muted-foreground">
                            Score: {{ item.score ?? '—' }}
                        </p>
                    </li>
                </ul>
                <div
                    v-else
                    class="rounded-xl border border-dashed border-border p-6 text-sm text-muted-foreground"
                >
                    Attempts and feedback will appear here as soon as you get started.
                </div>
            </div>
        </section>
    </AppLayout>
</template>
