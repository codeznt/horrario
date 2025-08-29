<template>
    <div class="form-field mb-4">
        <Label v-if="label" :for="id" class="mb-1 block text-sm font-medium">
            {{ label }}
            <span v-if="required" class="ml-1 text-red-500">*</span>
        </Label>

        <div class="relative">
            <slot />
        </div>

        <p v-if="error" class="mt-1 flex items-center gap-1 text-sm text-red-500">
            <Icon name="AlertCircle" class="h-3 w-3" />
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1 text-sm text-tg-hint">
            {{ hint }}
        </p>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Label } from '@/components/ui';

interface Props {
    id?: string;
    label?: string;
    error?: string;
    hint?: string;
    required?: boolean;
}

withDefaults(defineProps<Props>(), {
    required: false,
});
</script>
