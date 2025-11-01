<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { computed } from 'vue';

interface PracticeItem {
    id: number;
    objective_code: string;
    stem: string;
    type: string;
    rubric?: {
        name?: string | null;
        criteria?: Record<string, string> | null;
    } | null;
    meta?: Record<string, unknown> | null;
    priority: number;
    recommended_level: string;
    reason?: string;
}

const props = defineProps<{
    item: PracticeItem;
}>();

const emit = defineEmits<{
    practice: [item: PracticeItem];
    previewRubric: [item: PracticeItem];
}>();

const levelLabel = computed(() => props.item.recommended_level.replace(/_/g, ' '));

function onPractice(): void {
    emit('practice', props.item);
}

function onPreviewRubric(): void {
    emit('previewRubric', props.item);
}
</script>

<template>
    <Card class="h-full justify-between">
        <CardHeader class="space-y-2">
            <div class="flex items-center justify-between">
                <CardTitle class="text-base font-semibold">
                    {{ item.objective_code }}
                </CardTitle>
                <Badge variant="outline" class="capitalize">
                    {{ item.type }}
                </Badge>
            </div>
            <CardDescription class="line-clamp-3 text-sm text-muted-foreground">
                {{ item.stem }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3 text-sm">
            <p class="text-muted-foreground">
                Recommended mastery level: <span class="capitalize text-foreground">{{ levelLabel }}</span>
            </p>
            <p v-if="item.meta?.difficulty" class="text-muted-foreground">
                Difficulty: {{ item.meta.difficulty }}
            </p>
            <p v-if="item.reason" class="text-muted-foreground">
                {{ item.reason }}
            </p>
        </CardContent>
        <CardFooter class="flex flex-col gap-2 sm:flex-row">
            <Button class="flex-1" @click="onPractice">
                Practice now
            </Button>
            <Button variant="outline" class="flex-1" @click="onPreviewRubric">
                Preview rubric
            </Button>
        </CardFooter>
    </Card>
</template>
