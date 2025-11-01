<script setup lang="ts">
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogScrollContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { computed } from 'vue';

interface AttemptRecord {
    response?: string | null;
    response_preview?: string | null;
    score: number | null;
    submitted_at?: string | null;
}

interface ItemRecord {
    objective_code?: string | null;
    stem?: string | null;
    type?: string | null;
    rubric?: {
        name?: string | null;
    } | null;
}

interface FeedbackRecord {
    id: number;
    summary?: string | null;
    action_steps?: string[];
    strengths?: string | null;
    human_revision?: string | null;
    released_at?: string | null;
    attempt: AttemptRecord;
    item: ItemRecord;
}

const props = defineProps<{
    open: boolean;
    feedback: FeedbackRecord | null;
}>();

const emit = defineEmits<{
    'update:open': [open: boolean];
}>();

const prettySubmission = computed(() => {
    const value = props.feedback?.attempt.submitted_at;

    if (!value) {
        return 'Pending submission';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
});

function close(): void {
    emit('update:open', false);
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-h-[90vh] min-w-[min(640px,100vw)]">
            <DialogHeader class="space-y-2">
                <DialogTitle class="text-lg font-semibold">
                    {{ feedback?.item.objective_code }}
                </DialogTitle>
                <DialogDescription class="text-sm text-muted-foreground">
                    {{ feedback?.item.stem }}
                </DialogDescription>
            </DialogHeader>

            <section class="space-y-3 text-sm">
                <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                    <span>Submission: {{ prettySubmission }}</span>
                    <span>Score: {{ feedback?.attempt.score ?? '—' }}</span>
                    <span>Rubric: {{ feedback?.item.rubric?.name ?? '—' }}</span>
                </div>

                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-foreground">
                        Student response
                    </h3>
                    <p class="whitespace-pre-line rounded-lg border bg-muted/40 p-3 text-sm text-foreground">
                        {{ feedback?.attempt.response ?? 'Response not recorded.' }}
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-foreground">
                        AI summary
                    </h3>
                    <p class="rounded-lg bg-primary/5 p-3 text-sm text-foreground">
                        {{ feedback?.summary ?? 'Feedback is being prepared.' }}
                    </p>
                </div>

                <div v-if="feedback?.action_steps?.length" class="space-y-2">
                    <h3 class="text-sm font-semibold text-foreground">Action steps</h3>
                    <ul class="list-disc space-y-1 pl-5 text-muted-foreground">
                        <li v-for="step in feedback.action_steps" :key="step">
                            {{ step }}
                        </li>
                    </ul>
                </div>

                <div v-if="feedback?.human_revision" class="space-y-2">
                    <h3 class="text-sm font-semibold text-foreground">Teacher note</h3>
                    <p class="rounded-lg bg-secondary/30 p-3 text-sm text-foreground">
                        {{ feedback.human_revision }}
                    </p>
                </div>
            </section>

            <footer class="mt-6 flex justify-end">
                <button
                    type="button"
                    class="rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-muted"
                    @click="close"
                >
                    Close
                </button>
            </footer>
        </DialogContent>
    </Dialog>
</template>
