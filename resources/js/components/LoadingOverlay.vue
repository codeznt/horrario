<template>
    <Teleport to="body">
        <div v-if="show" class="loading-overlay">
            <div class="loading-content">
                <Spinner :size="spinnerSize" />
                <p v-if="message" class="mt-3 text-center text-sm">{{ message }}</p>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { Spinner } from '@/components/ui';

interface Props {
    show?: boolean;
    message?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
}

const props = withDefaults(defineProps<Props>(), {
    show: false,
    size: 'lg',
});

const spinnerSize = props.size;
</script>

<style scoped>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}

.loading-content {
    background: var(--color-tg-bg, var(--background));
    color: var(--color-tg-text, var(--foreground));
    padding: 2rem;
    border-radius: 0.75rem;
    text-align: center;
    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
    min-width: 200px;
}
</style>
