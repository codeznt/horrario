<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { backButton, miniApp } from '@telegram-apps/sdk';
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import { computed } from 'vue';

// shadcn-vue components
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Skeleton } from '@/components/ui/skeleton';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Badge } from '@/components/ui/badge';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { dashboard } from '@/routes';

// Icons
import { Check, X } from 'lucide-vue-next';

const support = {
    miniApp: typeof miniApp.isSupported === 'function' ? miniApp.isSupported() : true,
    backButton: typeof backButton.isSupported === 'function' ? backButton.isSupported() : true,
    themeParams: true,
    viewport: true,
};

const supportItems = computed(() => [
    { label: 'miniApp', value: support.miniApp },
    { label: 'backButton', value: support.backButton },
    { label: 'themeParams', value: support.themeParams },
    { label: 'viewport', value: support.viewport },
]);

function onPrimary(): void {
    // Demo handler
    // eslint-disable-next-line no-console
    console.log('Primary clicked');
}

function onNeutral(): void {
    // eslint-disable-next-line no-console
    console.log('Neutral clicked');
}

function onDestructive(): void {
    // eslint-disable-next-line no-console
    console.log('Destructive clicked');
}

function onLink(): void {
    // eslint-disable-next-line no-console
    console.log('Link clicked');
}

function goDashboard(): void {
    window.location.href = dashboard().url as string;
}

</script>

<template>
    <Head title="Welcome" />

    <TelegramAppLayout>
        <section id="tokens" class="mx-auto max-w-5xl space-y-6">
            <!-- Hero / Intro -->
            <Card class="relative overflow-hidden border border-tg-section-separator bg-tg-section-bg">
                <div class="pointer-events-none absolute inset-0 -z-10 opacity-70 [background:radial-gradient(1200px_400px_at_0%_0%,color-mix(in_oklab,var(--color-tg-accent),transparent_70%),transparent),radial-gradient(800px_300px_at_100%_100%,color-mix(in_oklab,var(--color-tg-link),transparent_75%),transparent)]" />
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-black/5 dark:bg-white/10">
                        <AppLogoIcon class="size-8 fill-current text-black dark:text-white" />
                    </div>
                    <div class="flex-1">
                        <h1 class="text-base font-semibold">Welcome to Horrario</h1>
                        <p class="text-xs text-tg-subtitle-text">Telegram Mini App-ready UI powered by shadcn-vue</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge class="hidden sm:inline-flex" variant="secondary">Preview</Badge>
                        <Button class="bg-tg-accent text-tg-accent-foreground shadow hover:opacity-90" @click="onPrimary">Get Started</Button>
                    </div>
                </CardContent>
            </Card>
            <!-- SDK Support -->
            <Card class="border border-tg-section-separator bg-tg-secondary-bg/60 backdrop-blur-sm">
                <CardHeader>
                    <CardTitle class="text-sm font-medium">SDK Support</CardTitle>
                    <CardDescription>Detection uses isSupported(). Mocked in dev if not in Telegram.</CardDescription>
                </CardHeader>
                <CardContent>
                    <TransitionGroup name="list" tag="ul" class="mt-1 grid grid-cols-2 gap-2 text-sm md:grid-cols-4">
                        <li
                            v-for="item in supportItems"
                            :key="item.label"
                            class="group flex items-center justify-between rounded-md border border-tg-section-separator bg-tg-section-bg/70 px-3 py-2 transition-all duration-200 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5 hover:bg-tg-section-bg"
                        >
                            <span class="font-medium">{{ item.label }}</span>
                            <span class="ml-2 inline-flex items-center gap-1 text-xs">
                                <Check v-if="item.value" class="size-4 text-green-500" />
                                <X v-else class="size-4 text-red-500" />
                                <Badge :variant="item.value ? 'secondary' : 'destructive'" class="px-2 py-0.5">{{ item.value ? 'Yes' : 'No' }}</Badge>
                            </span>
                        </li>
                    </TransitionGroup>
                </CardContent>
            </Card>

            <!-- Actions & Links -->
            <Card class="border border-tg-section-separator bg-tg-secondary-bg/60">
                <CardHeader>
                    <CardTitle class="text-sm font-medium">Actions & Links</CardTitle>
                    <CardDescription>Primary and link styles from Telegram tokens.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="mt-1 flex flex-wrap items-center gap-3">
                        <Button class="bg-tg-accent text-tg-accent-foreground shadow hover:opacity-90" @click="onPrimary">Primary</Button>
                        <Button variant="outline" class="border-tg-section-separator bg-tg-section-bg" @click="onNeutral">Neutral</Button>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="ghost" class="text-tg-destructive" @click="onDestructive">Destructive (text)</Button>
                                </TooltipTrigger>
                                <TooltipContent class="text-xs">Example action; no real effect</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <Button variant="link" class="text-tg-link" @click.prevent="onLink">Link</Button>
                        <span class="text-xs text-tg-hint">Hint text</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Tiles Grid -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="border border-tg-section-separator bg-tg-section-bg transition-transform duration-200 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5">
                    <CardContent class="p-4">
                        <h3 class="text-sm font-semibold">Section BG</h3>
                        <p class="mt-1 text-xs text-tg-hint">bg-tg-section-bg + border-tg-section-separator</p>
                    </CardContent>
                </Card>
                <Card class="border border-tg-section-separator bg-tg-secondary-bg transition-transform duration-200 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5">
                    <CardContent class="p-4">
                        <h3 class="text-sm font-semibold">Secondary BG</h3>
                        <p class="mt-1 text-xs text-tg-hint">bg-tg-secondary-bg</p>
                    </CardContent>
                </Card>
                <Card class="border border-tg-section-separator transition-transform duration-200 ease-[cubic-bezier(0.4,0,0.2,1)] hover:-translate-y-0.5">
                    <CardContent class="rounded-lg p-4 [background:linear-gradient(90deg,var(--color-tg-accent),var(--color-tg-link))] text-white">
                        <h3 class="text-sm font-semibold">Gradient Accent → Link</h3>
                        <p class="mt-1 text-xs text-white/80">using CSS vars directly</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Viewport Height Demo -->
            <Card class="border border-tg-section-separator bg-tg-section-bg">
                <CardHeader>
                    <CardTitle class="text-sm font-medium">Viewport Height</CardTitle>
                    <CardDescription>min-h-[--spacing-viewport-h] adapts to Telegram viewport.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Separator class="my-3 opacity-40" />
                    <div class="mt-3 min-h-[--spacing-viewport-h] overflow-hidden rounded-md border border-dashed border-tg-section-separator p-3">
                        <div class="relative">
                            <Skeleton class="mb-2 h-6 w-40 rounded-md opacity-60" />
                            <p class="text-xs text-tg-subtitle-text">This box grows to the Telegram viewport height.</p>
                            <div
                                class="absolute inset-0 -z-10 animate-pulse opacity-20 [background:repeating-linear-gradient(45deg,var(--color-tg-link),var(--color-tg-link)_10px,transparent_10px,transparent_20px)]"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </section>

        <template #actions>
            <Button class="bg-tg-accent text-tg-accent-foreground" @click="goDashboard">Open Dashboard</Button>
            <Button variant="outline" class="border-tg-section-separator bg-tg-section-bg">Later</Button>
        </template>
    </TelegramAppLayout>

</template>

<style scoped>
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateY(4px);
}
.list-enter-active,
.list-leave-active {
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
