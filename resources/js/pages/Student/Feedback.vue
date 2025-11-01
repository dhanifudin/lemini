<script setup lang="ts">
import FeedbackCard from '@/components/student/FeedbackCard.vue';
import AttemptDrawer from '@/components/student/AttemptDrawer.vue';
import ReflectionModal from '@/components/student/ReflectionModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useStudentStore, type FeedbackRecord } from '@/stores/student';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const store = useStudentStore();

const activeTab = ref<'published' | 'draft'>('published');
const attemptDrawerOpen = ref(false);
const reflectionOpen = ref(false);
const selectedFeedback = ref<FeedbackRecord | null>(null);

const published = computed(() => store.publishedFeedback);
const drafts = computed(() => store.draftFeedback);
const isLoading = computed(() => store.loading.feedback && !store.feedback);

onMounted(async () => {
    await store.fetchFeedback();
});

function handleView(feedback: FeedbackRecord): void {
    selectedFeedback.value = feedback;
    attemptDrawerOpen.value = true;
}

function handleReflect(feedback: FeedbackRecord): void {
    selectedFeedback.value = feedback;
    reflectionOpen.value = true;
}

async function submitReflection(payload: { note: string }): Promise<void> {
    if (!selectedFeedback.value) {
        return;
    }

    try {
        await store.submitReflection(selectedFeedback.value.id, payload.note);
        reflectionOpen.value = false;
        await store.fetchFeedback(true);
    } catch (error) {
        console.error(error);
    }
}
</script>

<template>
    <Head title="Feedback Center" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <section class="flex flex-col gap-6 px-4 pb-8 pt-4 lg:px-6">
            <header class="space-y-2">
                <h1 class="text-2xl font-semibold text-foreground">Feedback Center</h1>
                <p class="text-sm text-muted-foreground">
                    Review AI insights, teacher notes, and log reflections to track your growth.
                </p>
            </header>

            <nav class="flex items-center gap-2 rounded-full border border-border bg-muted/40 p-1 text-sm">
                <button
                    type="button"
                    class="flex-1 rounded-full px-4 py-2 font-medium transition"
                    :class="activeTab === 'published' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground'"
                    @click="activeTab = 'published'"
                >
                    Published
                    <span class="ml-1 text-xs text-muted-foreground">({{ published.length }})</span>
                </button>
                <button
                    type="button"
                    class="flex-1 rounded-full px-4 py-2 font-medium transition"
                    :class="activeTab === 'draft' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground'"
                    @click="activeTab = 'draft'"
                >
                    Drafts
                    <span class="ml-1 text-xs text-muted-foreground">({{ drafts.length }})</span>
                </button>
            </nav>

            <div v-if="isLoading" class="grid gap-3">
                <div class="h-48 animate-pulse rounded-xl bg-muted/40" />
                <div class="h-48 animate-pulse rounded-xl bg-muted/40" />
            </div>

            <div v-else class="grid gap-4">
                <FeedbackCard
                    v-for="feedback in (activeTab === 'published' ? published : drafts)"
                    :key="feedback.id"
                    :feedback="feedback"
                    @view="handleView"
                    @reflect="handleReflect"
                />

                <p
                    v-if="(activeTab === 'published' ? published : drafts).length === 0"
                    class="rounded-xl border border-dashed border-border p-6 text-sm text-muted-foreground"
                >
                    {{
                        activeTab === 'published'
                            ? 'Once feedback is released, it will appear here with action steps.'
                            : 'We\'ll show drafts while teachers are reviewing your submissions.'
                    }}
                </p>
            </div>

            <AttemptDrawer
                :open="attemptDrawerOpen"
                :feedback="selectedFeedback"
                @update:open="attemptDrawerOpen = $event"
            />

            <ReflectionModal
                :open="reflectionOpen"
                :loading="store.loading.reflection"
                :feedback-objective="selectedFeedback?.item.objective_code ?? null"
                @update:open="reflectionOpen = $event"
                @submit="submitReflection"
            />
        </section>
    </AppLayout>
</template>
