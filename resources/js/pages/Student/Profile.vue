<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { AppPageProps, BreadcrumbItemType } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

const page = usePage<AppPageProps>();

const user = computed(() => page.props.auth.user);
const digestOptIn = ref(true);
const releaseAlerts = ref(true);
</script>

<template>
    <Head title="Profile" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <section class="flex flex-col gap-6 px-4 pb-8 pt-4 lg:px-6">
            <header class="space-y-2">
                <h1 class="text-2xl font-semibold text-foreground">Profile & Settings</h1>
                <p class="text-sm text-muted-foreground">
                    Keep your contact information up to date and choose how you want to hear about new feedback.
                </p>
            </header>

            <div class="grid gap-6 md:grid-cols-2">
                <section class="rounded-xl border border-border bg-background/60 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-foreground">Account details</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Name</dt>
                            <dd class="text-foreground">{{ user.name }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Email</dt>
                            <dd class="text-foreground">{{ user.email }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="text-xs uppercase tracking-wide text-muted-foreground">Role</dt>
                            <dd class="capitalize text-foreground">{{ user.role ?? 'student' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-xl border border-border bg-background/60 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-foreground">Notifications</h2>
                    <div class="mt-4 space-y-4 text-sm">
                        <label class="flex items-start gap-3">
                            <input
                                v-model="releaseAlerts"
                                type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
                            />
                            <span>
                                <span class="font-medium text-foreground">Feedback release alerts</span>
                                <p class="text-xs text-muted-foreground">
                                    Receive an email when AI or teacher feedback is published.
                                </p>
                            </span>
                        </label>
                        <label class="flex items-start gap-3">
                            <input
                                v-model="digestOptIn"
                                type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary"
                            />
                            <span>
                                <span class="font-medium text-foreground">Weekly progress digest</span>
                                <p class="text-xs text-muted-foreground">
                                    A quick snapshot of mastery gains, attempts, and recommended focus areas.
                                </p>
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            <section class="rounded-xl border border-border bg-background/60 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-foreground">Export progress</h2>
                <p class="mt-2 text-sm text-muted-foreground">
                    Download a summary of your mastery levels and recent attempts to share with teachers or guardians.
                </p>
                <button
                    type="button"
                    class="mt-4 inline-flex items-center gap-2 rounded-md border border-border px-4 py-2 text-sm font-medium text-primary transition hover:bg-primary/10"
                >
                    Download progress (PDF)
                </button>
            </section>
        </section>
    </AppLayout>
</template>
