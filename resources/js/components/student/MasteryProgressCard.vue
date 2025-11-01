<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { computed } from 'vue';

const props = defineProps<{
    objectiveCode: string;
    level: string;
    progressPercent: number;
    lastSeenAt?: string | null;
}>();

const clampedProgress = computed(() => {
    const percent = Number.isFinite(props.progressPercent)
        ? props.progressPercent
        : 0;

    return Math.min(Math.max(percent, 0), 100);
});

const roundedProgress = computed(() => Math.round(clampedProgress.value));

const formattedLastSeen = computed(() => {
    if (!props.lastSeenAt) {
        return 'Not yet attempted';
    }

    const date = new Date(props.lastSeenAt);

    if (Number.isNaN(date.getTime())) {
        return 'Not yet attempted';
    }

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
    }).format(date);
});
</script>

<template>
    <Card class="gap-4">
        <CardHeader class="space-y-2">
            <CardTitle class="text-base font-semibold">
                {{ objectiveCode }}
            </CardTitle>
            <CardDescription class="flex items-center justify-between gap-2">
                <span class="capitalize text-sm text-muted-foreground">
                    {{ level || 'Unseen' }}
                </span>
                <span class="text-xs text-muted-foreground">
                    Last seen: {{ formattedLastSeen }}
                </span>
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                <div
                    class="h-full rounded-full bg-primary transition-all"
                    :style="{ width: clampedProgress + '%' }"
                ></div>
            </div>
            <p class="text-xs font-medium text-muted-foreground">
                {{ roundedProgress }}% toward mastery
            </p>
        </CardContent>
    </Card>
</template>
