<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogScrollContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { ref, watch } from 'vue';

const props = defineProps<{
    open: boolean;
    feedbackObjective?: string | null;
    loading?: boolean;
}>();

const emit = defineEmits<{
    'update:open': [open: boolean];
    submit: [payload: { note: string }];
}>();

const note = ref('');

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) {
            note.value = '';
        }
    },
);

function handleSubmit(): void {
    if (!note.value.trim()) {
        return;
    }

    emit('submit', { note: note.value.trim() });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader class="space-y-3">
                <DialogTitle class="text-lg font-semibold">
                    Reflect on your learning
                </DialogTitle>
                <DialogDescription class="text-sm text-muted-foreground">
                    {{
                        feedbackObjective
                            ? `What did you learn from ${feedbackObjective}?`
                            : 'Capture a quick note about what you learned.'
                    }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground" for="reflection-note">
                    Reflection note
                </label>
                <textarea
                    id="reflection-note"
                    v-model="note"
                    rows="4"
                    class="w-full resize-y rounded-md border border-input bg-background p-3 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                    placeholder="Summarize what went well, what was challenging, and what you will try next."
                ></textarea>
                <p class="text-xs text-muted-foreground">
                    Students who write reflections retain up to 23% more—share one or two takeaways.
                </p>
            </div>

            <DialogFooter class="mt-4 gap-2">
                <Button variant="outline" @click="emit('update:open', false)">
                    Cancel
                </Button>
                <Button :disabled="loading || !note.trim()" @click="handleSubmit">
                    {{ loading ? 'Saving...' : 'Save reflection' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
