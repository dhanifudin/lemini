<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import type { PendingFeedbackItem } from '@/stores/teacher';
import { computed } from 'vue';

const props = defineProps<{
    items: PendingFeedbackItem[];
    loading: boolean;
    selectedIds: number[];
    moderationBusy: boolean;
    reminderBusy: boolean;
}>();

const emit = defineEmits<{
    toggle: [feedbackId: number];
    approve: [feedbackId: number];
    revise: [feedbackId: number];
    remind: [];
}>();

const allSelected = computed(() => props.items.length > 0 && props.items.every((item) => props.selectedIds.includes(item.id)));

function toggleAll(): void {
    if (allSelected.value) {
        props.items.forEach((item) => emit('toggle', item.id));
    } else {
        props.items
            .filter((item) => !props.selectedIds.includes(item.id))
            .forEach((item) => emit('toggle', item.id));
    }
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
}
</script>

<template>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <label class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        :checked="allSelected"
                        @change="toggleAll"
                        class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                    />
                    Select all
                </label>
                <span v-if="selectedIds.length" class="text-xs">
                    {{ selectedIds.length }} selected
                </span>
            </div>
            <Button
                variant="outline"
                size="sm"
                :disabled="!selectedIds.length || reminderBusy"
                @click="emit('remind')"
            >
                {{ reminderBusy ? 'Sending…' : 'Send reminder' }}
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-muted/40 text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            Student / Objective
                        </th>
                        <th class="px-4 py-3 text-left">
                            Submitted
                        </th>
                        <th class="px-4 py-3 text-left">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left">
                            Notes
                        </th>
                        <th class="px-4 py-3 text-right">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="loading">
                        <td colspan="5" class="p-4">
                            <div class="space-y-2">
                                <Skeleton class="h-4 w-full" />
                                <Skeleton class="h-4 w-3/4" />
                                <Skeleton class="h-4 w-2/3" />
                            </div>
                        </td>
                    </tr>
                    <tr v-for="item in items" :key="item.id" class="hover:bg-muted/30">
                        <td class="px-4 py-3 align-top">
                            <div class="flex items-start gap-2">
                                <input
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                    :checked="selectedIds.includes(item.id)"
                                    @change="emit('toggle', item.id)"
                                />
                                <div class="space-y-1">
                                    <p class="font-medium text-foreground">
                                        {{ item.student.name ?? 'Unknown student' }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.item.objective_code ?? '—' }} · {{ item.item.rubric ?? 'No rubric' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 align-top text-sm text-muted-foreground">
                            {{ formatDate(item.attempt.submitted_at) }}
                        </td>
                        <td class="px-4 py-3 align-top text-sm">
                            <span class="rounded-full bg-secondary/40 px-2 py-1 text-xs font-medium uppercase tracking-wide text-secondary-foreground">
                                {{ item.status }}
                            </span>
                            <p v-if="item.needs_reflection" class="mt-1 text-xs text-amber-600">
                                Waiting on student reflection
                            </p>
                        </td>
                        <td class="px-4 py-3 align-top text-xs text-muted-foreground">
                            <p class="line-clamp-3">
                                {{ item.attempt.response_preview }}
                            </p>
                            <p v-if="item.last_action" class="mt-2 text-[11px] uppercase tracking-wide text-muted-foreground">
                                Last action: {{ item.last_action.action }} · {{ item.last_action.by ?? 'Unknown' }} ·
                                {{ formatDate(item.last_action.at) }}
                            </p>
                        </td>
                        <td class="px-4 py-3 align-top text-right text-sm">
                            <div class="flex flex-col items-end gap-2">
                                <Button
                                    size="sm"
                                    :disabled="moderationBusy"
                                    @click="emit('approve', item.id)"
                                >
                                    Approve
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    :disabled="moderationBusy"
                                    @click="emit('revise', item.id)"
                                >
                                    Request revision
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!loading && !items.length">
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-muted-foreground">
                            All caught up! No feedback awaiting review.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
