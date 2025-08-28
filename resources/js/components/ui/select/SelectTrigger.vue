<script setup lang="ts">
import { inject, computed } from 'vue'
import { cn } from '@/lib/utils'
import Icon from '@/components/Icon.vue'

interface SelectTriggerProps {
  class?: string
}

defineProps<SelectTriggerProps>()

const select = inject('select', {
  open: { value: false },
  modelValue: { value: '' },
  updateModelValue: () => {}
})

const open = computed(() => select.open.value)

function toggleOpen() {
  select.open.value = !select.open.value
}
</script>

<template>
  <button
    type="button"
    @click="toggleOpen"
    :class="cn(
      'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      open ? 'ring-2 ring-ring ring-offset-2' : '',
      $props.class
    )"
    aria-haspopup="listbox"
    :aria-expanded="open"
  >
    <slot></slot>
    <Icon name="ChevronDown" class="h-4 w-4 opacity-50" />
  </button>
</template>
