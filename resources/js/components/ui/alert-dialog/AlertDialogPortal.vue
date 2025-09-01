<template>
  <DialogPortal :class="portalClass">
    <slot />
  </DialogPortal>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import DialogPortal from '../dialog/DialogPortal.vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const portalClass = computed(() => props.class || '');
</script>
