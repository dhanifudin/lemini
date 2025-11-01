<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogScrollContent.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { computed, ref } from 'vue';

interface ObjectiveOption {
    code: string;
    label?: string;
}

const props = defineProps<{
    open: boolean;
    objectives: ObjectiveOption[];
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    start: [payload: { size: number; objectives: string[]; timer_minutes?: number | null }];
}>();

const size = ref(5);
const timerMinutes = ref<number | null>(null);
const selectedObjectives = ref<string[]>([]);

const objectiveOptions = computed(() => props.objectives ?? []);

function toggleObjective(code: string): void {
    if (selectedObjectives.value.includes(code)) {
        selectedObjectives.value = selectedObjectives.value.filter((current) => current !== code);
    } else {
        selectedObjectives.value = [...selectedObjectives.value, code];
    }
}

function close(): void {
    emit('update:open', false);
}

function startQuiz(): void {
    emit('start', {
        size: size.value,
        objectives: selectedObjectives.value,
        timer_minutes: timerMinutes.value ?? undefined,
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="space-y-2">
                <DialogTitle class="text-lg font-semibold">Start a Quiz Session</DialogTitle>
                <p class="text-sm text-muted-foreground">
                    Choose the number of questions and, optionally, the objectives you want to focus on.
                </p>
            </DialogHeader>

            <form class="space-y-6" @submit.prevent="startQuiz">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="quiz-size">
                        Number of questions
                    </label>
                    <input
                        id="quiz-size"
                        v-model.number="size"
                        type="number"
                        min="1"
                        max="20"
                        class="w-24 rounded-md border border-input bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                    />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground" for="quiz-timer">
                        Timer (minutes)
                    </label>
                    <input
                        id="quiz-timer"
                        v-model.number="timerMinutes"
                        type="number"
                        min="1"
                        max="120"
                        placeholder="Optional"
                        class="w-32 rounded-md border border-input bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                    />
                </div>

                <div class="space-y-3">
                    <p class="text-sm font-medium text-foreground">Focus objectives</p>
                    <div v-if="objectiveOptions.length" class="space-y-2">
                        <label
                            v-for="objective in objectiveOptions"
                            :key="objective.code"
                            class="flex items-center gap-2 text-sm text-muted-foreground"
                        >
                            <input
                                type="checkbox"
                                class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                :checked="selectedObjectives.includes(objective.code)"
                                @change="toggleObjective(objective.code)"
                            />
                            <span>
                                {{ objective.label || objective.code }}
                            </span>
                        </label>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        No objectives available yet. Start a quiz to let the adaptive engine pick for you.
                    </p>
                </div>

                <DialogFooter class="gap-2">
                    <Button type="button" variant="outline" @click="close">Cancel</Button>
                    <Button type="submit">Start quiz</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
