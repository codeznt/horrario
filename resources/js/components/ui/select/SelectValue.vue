<script setup lang="ts">
import { inject, computed } from 'vue'

interface SelectValueProps {
  placeholder?: string
}

const props = defineProps<SelectValueProps>()

const select = inject('select', {
  modelValue: { value: '' },
  open: { value: false },
  updateModelValue: () => {}
})

const hasValue = computed(() => !!select.modelValue.value)
</script>

<template>
  <span class="block truncate">
    <slot v-if="hasValue"></slot>
    <span v-else class="text-muted-foreground">{{ placeholder }}</span>
  </span>
</template>
