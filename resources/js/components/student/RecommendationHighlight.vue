<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

interface RecommendationPayload {
    focus_area?: string;
    suggested_resource?: string;
    next_check_in?: string;
}

const props = defineProps<{
    title?: string;
    payload?: RecommendationPayload | null;
    primaryPath?: string;
}>();

const formattedCheckIn = computed(() => {
    if (!props.payload?.next_check_in) {
        return 'Flexible timeline';
    }

    const date = new Date(props.payload.next_check_in);

    if (Number.isNaN(date.getTime())) {
        return props.payload.next_check_in;
    }

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
    }).format(date);
});

function goToPrimary(): void {
    if (props.primaryPath) {
        router.visit(props.primaryPath);
    }
}
</script>

<template>
    <Card class="bg-gradient-to-br from-primary/10 via-background to-background">
        <CardHeader class="space-y-3">
            <CardTitle class="text-lg font-semibold">
                {{ title ?? 'Your next best action' }}
            </CardTitle>
            <CardDescription class="max-w-xl text-sm text-muted-foreground">
                {{
                    payload?.focus_area
                        ?? 'Stay consistent with your biology practice to unlock new recommendations.'
                }}
            </CardDescription>
        </CardHeader>
        <CardContent class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-medium text-foreground">
                    Suggested resource
                </p>
                <p class="text-sm text-muted-foreground">
                    {{
                        payload?.suggested_resource
                            ?? 'Complete today\'s study plan and we\'ll recommend a resource to explore next.'
                    }}
                </p>
                <p class="text-xs text-muted-foreground">
                    Next check-in: {{ formattedCheckIn }}
                </p>
            </div>
            <Button
                v-if="primaryPath"
                class="self-start md:self-auto"
                @click="goToPrimary"
            >
                Start now
            </Button>
        </CardContent>
    </Card>
</template>
