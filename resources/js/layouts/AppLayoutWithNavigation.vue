<template>
    <TelegramAppLayout 
        :padded="padded" 
        :fill="fill" 
        :class="cn('app-with-nav', props.class)"
    >
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
import { cn } from '@/lib/utils';

interface Props {
    /** Adds safe-area paddings around the content */
    padded?: boolean;
    /** Optional: force full-bleed background for Telegram theme */
    fill?: boolean;
    /** Optional extra classes for the root */
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    padded: true,
    fill: true,
    class: '',
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