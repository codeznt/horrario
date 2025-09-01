<template>
    <TelegramAppLayout 
        :padded="props.padded" 
        :fill="props.fill" 
        :class="cn('app-with-nav', props.class)"
    >
        <!-- Header with title and subtitle if provided -->
        <div v-if="props.title || props.subtitle" class="mb-4 px-4 pt-4">
            <div class="flex items-center">
                <Button 
                    v-if="props.showBackButton" 
                    variant="ghost" 
                    size="icon" 
                    class="mr-2" 
                    @click="goBack"
                >
                    <Icon name="ArrowLeft" class="h-5 w-5" />
                </Button>
                <div>
                    <h1 v-if="props.title" class="text-xl font-semibold">{{ props.title }}</h1>
                    <p v-if="props.subtitle" class="text-sm text-muted-foreground">{{ props.subtitle }}</p>
                </div>
            </div>
        </div>

        <!-- Content slot -->
        <div class="flex min-h-full flex-col pb-20">
            <slot />
        </div>
        
        <!-- Bottom Navigation -->
        <template #actions>
            <BottomNavigation />
        </template>
    </TelegramAppLayout>
</template>

<script setup lang="ts">
import TelegramAppLayout from '@/layouts/TelegramAppLayout.vue';
import BottomNavigation from '@/components/BottomNavigation.vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { cn } from '@/lib/utils';
import { router } from '@inertiajs/vue3';

const goBack = () => {
    history.back();
};

interface Props {
    /** Adds safe-area paddings around the content */
    padded?: boolean;
    /** Optional: force full-bleed background for Telegram theme */
    fill?: boolean;
    /** Optional extra classes for the root */
    class?: string;
    /** Page title to display in the header */
    title?: string;
    /** Subtitle to display below the title */
    subtitle?: string;
    /** Whether to show a back button */
    showBackButton?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    padded: true,
    fill: true,
    class: '',
    title: undefined,
    subtitle: undefined,
    showBackButton: false,
});
</script>

<style scoped>
/* Ensure content doesn't overlap with bottom navigation */
.app-with-nav .tma-content {
    padding-bottom: 80px; /* Space for bottom navigation */
}

/* Override the default actions styling to use full width for navigation */
:deep(.tma-actions) {
    padding: 0;
    background: transparent;
}
</style>