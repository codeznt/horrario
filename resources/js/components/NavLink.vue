<template>
    <Link
        :href="href"
        :class="[
            'nav-link flex min-w-0 flex-1 flex-col items-center justify-center p-3',
            'transition-colors duration-200',
            active ? 'text-tg-link' : 'text-tg-hint hover:text-tg-text',
        ]"
    >
        <Icon :name="icon" class="mb-1 h-5 w-5" />
        <span class="truncate text-xs font-medium">{{ label }}</span>
    </Link>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    href: string;
    active: boolean;
    icon: string;
    label: string;
}

defineProps<Props>();
</script>

<style scoped>
.nav-link {
    min-height: 60px;
}

/* Ensure proper touch targets on mobile */
@media (max-width: 640px) {
    .nav-link {
        min-height: 64px;
    }
}
</style>
