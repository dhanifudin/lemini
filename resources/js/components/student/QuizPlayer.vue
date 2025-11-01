<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import type { QuizSessionItemSummary } from '@/stores/quiz';
import { useQuizStore } from '@/stores/quiz';
import { computed, ref, watch } from 'vue';

const quizStore = useQuizStore();

const currentItem = computed(() => quizStore.currentItem);
const currentAnswer = ref<unknown>(null);
const summary = computed(() => quizStore.summary);

watch(
    () => quizStore.currentItem,
    (item) => {
        if (!item) {
            currentAnswer.value = null;
            return;
        }

        currentAnswer.value = quizStore.answers[item.session_item_id] ?? extractInitialAnswer(item.response);
    },
    { immediate: true },
);

function extractInitialAnswer(response: unknown): unknown {
    if (response === null || response === undefined) {
        return null;
    }

    if (Array.isArray(response)) {
        return response;
    }

    if (typeof response === 'object' && response !== null && 'value' in response) {
        return (response as Record<string, unknown>).value ?? null;
    }

    return response;
}

async function handleAnswerChange(value: unknown): Promise<void> {
    const item = currentItem.value;
    if (!item) {
        return;
    }

    currentAnswer.value = value;
    await quizStore.answerCurrent(value);
}

async function submitQuiz(): Promise<void> {
    await quizStore.submitQuiz();
}

const isSubmitted = computed(() => quizStore.session?.status === 'submitted');

function responseLabel(item: QuizSessionItemSummary): string {
    const value = extractInitialAnswer(item.response);

    if (value === null || value === undefined || value === '') {
        return 'No response';
    }

    if (Array.isArray(value)) {
        return value.join(', ');
    }

    return String(value);
}

function statusBadgeClasses(status: string): string {
    switch (status) {
        case 'correct':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
        case 'incorrect':
            return 'bg-rose-100 text-rose-800 border border-rose-200';
        case 'pending_review':
            return 'bg-amber-100 text-amber-800 border border-amber-200';
        default:
            return 'bg-muted text-muted-foreground border border-border/60';
    }
}

function normalizeOptions(
    options: QuizSessionItemSummary['options'],
): Array<{ value: string; label: string; description?: string }> {
    if (!options) {
        return [];
    }

    if (Array.isArray(options)) {
        return options.map((option, index) => ({
            value: String(option),
            label: typeof option === 'string' ? option : `Option ${index + 1}`,
        }));
    }

    return Object.entries(options).map(([value, label]) => ({
        value,
        label: typeof label === 'string' ? label : value,
    }));
}
</script>

<template>
    <div v-if="quizStore.hasSession" class="relative rounded-xl border border-border bg-background/80 p-4 shadow-sm">
        <div class="flex items-center justify-between pb-3">
            <p class="text-sm text-muted-foreground">
                Status:
                <span :class="isSubmitted ? 'text-emerald-600 font-medium' : 'text-amber-600 font-medium'">
                    {{ quizStore.session?.status }}
                </span>
            </p>
        </div>

        <div v-if="isSubmitted" class="space-y-6">
            <Card class="border-none bg-primary/5 shadow-none">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">Quiz Results</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div class="text-sm text-muted-foreground">
                        <p class="text-xs uppercase tracking-wide">Average score</p>
                        <p class="text-xl font-semibold text-foreground">
                            {{ summary?.average_score ?? '—' }}
                        </p>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <p class="text-xs uppercase tracking-wide">Correct</p>
                        <p class="text-xl font-semibold text-emerald-600">
                            {{ summary?.correct ?? 0 }} / {{ summary?.total_questions ?? quizStore.items.length }}
                        </p>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <p class="text-xs uppercase tracking-wide">Incorrect</p>
                        <p class="text-xl font-semibold text-rose-600">
                            {{ summary?.incorrect ?? 0 }}
                        </p>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <p class="text-xs uppercase tracking-wide">Pending review</p>
                        <p class="text-xl font-semibold text-amber-600">
                            {{ summary?.pending_review ?? 0 }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <div class="space-y-4">
                <Card
                    v-for="item in quizStore.items"
                    :key="item.session_item_id"
                    class="border border-border/70"
                >
                    <CardHeader class="space-y-2">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-base font-semibold">Question {{ item.position }}</CardTitle>
                            <span :class="['rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide', statusBadgeClasses(item.status)]">
                                {{ item.status.replace('_', ' ') }}
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground">{{ item.stem }}</p>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div>
                            <p class="font-medium text-foreground">Your response</p>
                            <p class="text-muted-foreground">{{ responseLabel(item) }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Correct answer</p>
                            <p class="text-muted-foreground">{{ item.correct_answer ?? 'Pending review' }}</p>
                        </div>
                        <div v-if="item.rationale">
                            <p class="font-medium text-foreground">Explanation</p>
                            <p class="text-muted-foreground">{{ item.rationale }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <Card v-else-if="currentItem" class="border-none shadow-none">
            <CardHeader>
                <CardTitle class="text-lg font-semibold">
                    {{ currentItem.stem }}
                </CardTitle>
                <p class="text-sm text-muted-foreground">
                    Objective: {{ currentItem.objective_code }}
                    <span v-if="currentItem.learning_objective">— {{ currentItem.learning_objective }}</span>
                </p>
            </CardHeader>
            <CardContent class="space-y-4">
                <div v-if="currentItem.type === 'MCQ'" class="space-y-3">
                    <label
                        v-for="option in normalizeOptions(currentItem.options)"
                        :key="option.value"
                        class="flex items-start gap-3 rounded-lg border border-border/70 p-3 text-sm hover:border-primary/60"
                    >
                        <input
                            type="radio"
                            :name="`quiz-option-${currentItem.session_item_id}`"
                            class="mt-1 h-4 w-4 border-border text-primary focus:ring-primary"
                            :disabled="isSubmitted"
                            :value="option.value"
                            :checked="currentAnswer === option.value"
                            @change="handleAnswerChange(option.value)"
                        />
                        <span>
                            <strong>{{ option.label }}</strong>
                            <span v-if="option.description"> — {{ option.description }}</span>
                        </span>
                    </label>
                </div>

                <div v-else class="space-y-2">
                    <textarea
                        class="min-h-[180px] w-full resize-y rounded-md border border-border bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                        :disabled="isSubmitted"
                        :value="currentAnswer ?? ''"
                        @blur="handleAnswerChange(($event.target as HTMLTextAreaElement).value)"
                    ></textarea>
                    <p class="text-xs text-muted-foreground">
                        Short-answer response. Teachers will review detailed answers after quiz submission.
                    </p>
                </div>
            </CardContent>
            <CardFooter class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" :disabled="quizStore.currentIndex === 0" @click="quizStore.previous">
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="quizStore.currentIndex >= quizStore.items.length - 1"
                        @click="quizStore.next"
                    >
                        Next
                    </Button>
                </div>

                <Button :disabled="quizStore.loading.submit" @click="submitQuiz">
                    {{ quizStore.loading.submit ? 'Submitting…' : 'Submit quiz' }}
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>
