<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { backButton, miniApp } from '@telegram-apps/sdk';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';

const support = {
    miniApp: typeof miniApp.isSupported === 'function' ? miniApp.isSupported() : true,
    backButton: typeof backButton.isSupported === 'function' ? backButton.isSupported() : true,
    themeParams: true,
    viewport: true,
};

</script>

<template>
    <Head title="Welcome" />

        <TelegramAppLayout>
            <section id="tokens" class="mx-auto max-w-5xl space-y-6">
                <div class="rounded-lg border border-tg-section-separator bg-tg-secondary-bg p-4">
                    <h2 class="text-sm font-medium">SDK Support</h2>
                    <p class="mt-1 text-xs text-tg-hint">Detection uses isSupported(). Mocked in dev if not in Telegram.</p>
                    <ul class="mt-3 grid grid-cols-2 gap-2 text-sm md:grid-cols-4">
                        <li class="rounded-md border border-tg-section-separator bg-tg-section-bg px-3 py-2">miniApp: <span class="font-medium">{{ support.miniApp ? 'yes' : 'no' }}</span></li>
                        <li class="rounded-md border border-tg-section-separator bg-tg-section-bg px-3 py-2">backButton: <span class="font-medium">{{ support.backButton ? 'yes' : 'no' }}</span></li>
                        <li class="rounded-md border border-tg-section-separator bg-tg-section-bg px-3 py-2">themeParams: <span class="font-medium">{{ support.themeParams ? 'yes' : 'no' }}</span></li>
                        <li class="rounded-md border border-tg-section-separator bg-tg-section-bg px-3 py-2">viewport: <span class="font-medium">{{ support.viewport ? 'yes' : 'no' }}</span></li>
                    </ul>
                </div>

                <div class="rounded-lg border border-tg-section-separator bg-tg-secondary-bg p-4">
                    <h2 class="text-sm font-medium">Actions & Links</h2>
                    <p class="mt-1 text-xs text-tg-subtitle-text">Primary and link styles from Telegram tokens.</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <button class="rounded-md bg-tg-accent px-4 py-2 text-sm font-medium text-tg-accent-foreground shadow hover:opacity-90">Primary</button>
                        <button class="rounded-md border border-tg-section-separator bg-tg-section-bg px-4 py-2 text-sm">Neutral</button>
                        <button class="rounded-md px-4 py-2 text-sm text-tg-destructive">Destructive (text)</button>
                        <a href="#" class="text-sm text-tg-link underline">Link</a>
                        <span class="text-xs text-tg-hint">Hint text</span>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-tg-section-separator bg-tg-section-bg p-4">
                        <h3 class="text-sm font-semibold">Section BG</h3>
                        <p class="mt-1 text-xs text-tg-hint">bg-tg-section-bg + border-tg-section-separator</p>
                    </div>
                    <div class="rounded-lg border border-tg-section-separator bg-tg-secondary-bg p-4">
                        <h3 class="text-sm font-semibold">Secondary BG</h3>
                        <p class="mt-1 text-xs text-tg-hint">bg-tg-secondary-bg</p>
                    </div>
                    <div class="rounded-lg border border-tg-section-separator p-4 [background:linear-gradient(90deg,var(--color-tg-accent),var(--color-tg-link))]">
                        <h3 class="text-sm font-semibold text-white">Gradient Accent → Link</h3>
                        <p class="mt-1 text-xs text-white/80">using CSS vars directly</p>
                    </div>
                </div>

                <div class="rounded-lg border border-tg-section-separator bg-tg-section-bg p-4">
                    <h2 class="text-sm font-medium">Viewport Height</h2>
                    <p class="mt-1 text-xs text-tg-hint">min-h-[--spacing-viewport-h] adapts to Telegram viewport.</p>
                    <div class="mt-3 min-h-[--spacing-viewport-h] rounded-md border border-dashed border-tg-section-separator p-3">
                        <p class="text-xs text-tg-subtitle-text">This box grows to the Telegram viewport height.</p>
                    </div>
                </div>
            </section>
        </TelegramAppLayout>

</template>
