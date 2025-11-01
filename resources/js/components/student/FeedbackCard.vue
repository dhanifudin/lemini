<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { computed } from 'vue';

interface FeedbackItem {
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

const props = defineProps<{
    feedback: FeedbackItem;
}>();

const feedback = computed(() => props.feedback);

const emit = defineEmits<{
    view: [feedback: FeedbackItem];
    reflect: [feedback: FeedbackItem];
}>();

function formatTimestamp(timestamp?: string | null): string {
    if (!timestamp) {
        return 'Pending release';
    }

    const date = new Date(timestamp);

    if (Number.isNaN(date.getTime())) {
        return timestamp;
    }

    return `${date.toLocaleDateString()} @ ${date.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
    })}`;
}

function onView(): void {
    emit('view', props.feedback);
}

function onReflect(): void {
    emit('reflect', props.feedback);
}
</script>

<template>
    <Card class="gap-4">
        <CardHeader class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <CardTitle class="text-base font-semibold">
                    {{ props.feedback.item.objective_code }} ·
                    {{ props.feedback.item.rubric?.name }}
                </CardTitle>
                <Badge :variant="props.feedback.status === 'published' ? 'default' : 'secondary'">
                    {{ props.feedback.status }}
                </Badge>
            </div>
            <CardDescription class="line-clamp-3 text-sm">
                {{ props.feedback.summary ?? 'We\'ll summarize your submission once feedback is finalized.' }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            <div class="space-y-2 text-sm">
                <p v-if="feedback.action_steps?.length" class="font-semibold text-foreground">
                    Action steps
                </p>
                <ul v-if="feedback.action_steps?.length" class="list-disc space-y-1 pl-5 text-muted-foreground">
                    <li v-for="step in feedback.action_steps" :key="step">
                        {{ step }}
                    </li>
                </ul>
                <p v-if="feedback.strengths" class="text-muted-foreground">
                    Strengths: {{ feedback.strengths }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span>Submitted: {{ formatTimestamp(feedback.attempt.submitted_at) }}</span>
                <span>Feedback: {{ formatTimestamp(feedback.released_at ?? feedback.created_at) }}</span>
                <span>Score: {{ feedback.attempt.score ?? '—' }}</span>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <Button variant="outline" class="flex-1" @click="onView">
                    View attempt
                </Button>
                <Button class="flex-1" @click="onReflect">
                    Log reflection
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
