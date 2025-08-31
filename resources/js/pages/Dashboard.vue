<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import type { AppPageProps, User, BreadcrumbItemType } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const { t } = useTranslations();
const page = usePage<AppPageProps>();

const user = page.props.auth?.user;

// Define breadcrumbs for the dashboard
const breadcrumbs: BreadcrumbItemType[] = [
    { title: t('app.dashboard'), href: '/dashboard' }
];

onMounted(() => {
    // Route users to their role-specific dashboards
    if (user?.role === 'business') {
        router.visit('/business/dashboard', { replace: true });
    } else if (user?.role === 'customer') {
        router.visit('/customer/dashboard', { replace: true });
    }
    // Users without specific roles or with 'user' role stay on general dashboard
});
</script>

<template>
    <Head :title="t('app.dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Telegram theme demo block -->
            <section class="rounded-lg border border-tg-section-separator bg-tg-secondary-bg p-4 text-tg-text">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-medium">{{ t('app.telegram_theme_demo') }}</h2>
                    <button class="rounded-md bg-tg-accent px-3 py-1 text-sm text-tg-accent-foreground hover:opacity-90">
                        {{ t('app.action') }}
                    </button>
                </div>
                <p class="mt-2 text-xs text-tg-hint">{{ t('app.telegram_theme_description') }}</p>
            </section>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[--spacing-viewport-h] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
