<script setup lang="ts">
import PracticeItemCard from '@/components/student/PracticeItemCard.vue';
import QuizLauncherModal from '@/components/student/QuizLauncherModal.vue';
import QuizPlayer from '@/components/student/QuizPlayer.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogScrollContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useStudentStore, type PracticeItem } from '@/stores/student';
import { useQuizStore } from '@/stores/quiz';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const store = useStudentStore();
const quizStore = useQuizStore();

const selectedItem = ref<PracticeItem | null>(null);
const rubricOpen = ref(false);
const quizLauncherOpen = ref(false);

const queue = computed(() => store.practice?.queue ?? []);
const resources = computed(() => store.practice?.resources ?? []);
const isLoading = computed(() => store.loading.practice && !store.practice);
const quizObjectives = computed(() => {
    const map = new Map<string, string>();

    queue.value.forEach((item) => {
        map.set(item.objective_code, item.objective_code);
    });

    return Array.from(map.entries()).map(([code, label]) => ({ code, label }));
});

onMounted(async () => {
    await store.fetchPractice();
    if (!quizStore.session && queue.value.length === 0) {
        quizStore.reset();
    }
});

function handlePracticeNow(item: PracticeItem): void {
    selectedItem.value = item;
    rubricOpen.value = true;
}

function handlePreviewRubric(item: PracticeItem): void {
    selectedItem.value = item;
    rubricOpen.value = true;
}

async function startQuiz(payload: { size: number; objectives: string[]; timer_minutes?: number | null }): Promise<void> {
    try {
        await quizStore.startQuiz(payload);
        quizLauncherOpen.value = false;
    } catch (error) {
        console.error(error);
    }
}
</script>

<template>
    <Head title="Practice & Resources" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <section class="flex flex-col gap-6 px-4 pb-8 pt-4 lg:px-6">
            <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-foreground">Personalized Practice</h1>
                    <p class="text-sm text-muted-foreground">
                        Based on your mastery levels, we curated the next set of biology questions and resources.
                    </p>
                </div>
                <Button class="self-start" @click="quizLauncherOpen = true">Start quiz</Button>
            </header>

            <p v-if="quizStore.error" class="text-sm text-red-600">{{ quizStore.error }}</p>

            <QuizPlayer v-if="quizStore.hasSession" />

            <div class="space-y-3">
                <h2 class="text-lg font-semibold text-foreground">Practice queue</h2>
                <div v-if="isLoading" class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <div class="h-48 animate-pulse rounded-xl bg-muted/40" />
                    <div class="h-48 animate-pulse rounded-xl bg-muted/40" />
                    <div class="h-48 animate-pulse rounded-xl bg-muted/40" />
                </div>
                <div
                    v-else
                    class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                >
                    <PracticeItemCard
                        v-for="item in queue"
                        :key="item.id"
                        :item="item"
                        @practice="handlePracticeNow"
                        @preview-rubric="handlePreviewRubric"
                    />

                    <div
                        v-if="!queue.length"
                        class="rounded-xl border border-dashed border-border p-6 text-sm text-muted-foreground"
                    >
                        You are all caught up! Revisit feedback or continue exploring the resources below.
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <h2 class="text-lg font-semibold text-foreground">Supplemental resources</h2>
                <ul class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <li
                        v-for="resource in resources"
                        :key="resource.url"
                        class="rounded-xl border border-border bg-background/60 p-4 text-sm shadow-sm"
                    >
                        <p class="text-xs uppercase tracking-wide text-muted-foreground">
                            {{ resource.type }} · {{ resource.topic }}
                        </p>
                        <a
                            class="mt-2 block text-base font-semibold text-primary hover:underline"
                            :href="resource.url"
                            target="_blank"
                            rel="noreferrer"
                        >
                            {{ resource.title }}
                        </a>
                    </li>
                </ul>
            </div>

            <Dialog :open="rubricOpen" @update:open="rubricOpen = $event">
                <DialogContent class="sm:max-w-xl">
                    <DialogHeader class="space-y-2">
                        <DialogTitle class="text-lg font-semibold">
                            {{ selectedItem?.objective_code }}
                        </DialogTitle>
                        <DialogDescription class="text-sm text-muted-foreground">
                            {{ selectedItem?.rubric?.name ?? 'Rubric details' }}
                        </DialogDescription>
                    </DialogHeader>
                    <div class="space-y-3 text-sm">
                        <p class="whitespace-pre-line text-muted-foreground">
                            {{ selectedItem?.stem }}
                        </p>
                        <div v-if="selectedItem?.rubric?.criteria" class="space-y-2">
                            <h3 class="text-sm font-semibold text-foreground">Criteria</h3>
                            <ul class="list-disc space-y-1 pl-5 text-muted-foreground">
                                <li
                                    v-for="(description, name) in selectedItem.rubric.criteria"
                                    :key="name"
                                >
                                    <span class="font-medium text-foreground">{{ name }}:</span>
                                    {{ description }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>

            <QuizLauncherModal
                :open="quizLauncherOpen"
                :objectives="quizObjectives"
                @update:open="quizLauncherOpen = $event"
                @start="startQuiz"
            />
        </section>
    </AppLayout>
</template>
