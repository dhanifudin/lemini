<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';

const props = defineProps<{
    title: string;
    value: number | string | null;
    description?: string;
}>();
</script>

<template>
    <Card class="gap-2">
        <CardHeader class="space-y-1">
            <CardTitle class="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                {{ title }}
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-2">
            <p class="text-3xl font-semibold text-foreground">
                {{ value ?? '—' }}
            </p>
            <CardDescription v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </CardDescription>
        </CardContent>
    </Card>
</template>
