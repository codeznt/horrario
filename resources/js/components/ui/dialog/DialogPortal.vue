<template>
  <Teleport to="body" :disabled="disabled">
    <div :class="portalClass">
      <slot />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  class?: string;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false
});

const portalClass = computed(() => props.class || '');
</script>
