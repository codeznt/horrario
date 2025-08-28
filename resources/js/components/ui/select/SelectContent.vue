<script setup lang="ts">
import { inject, computed, ref, onMounted, onUnmounted } from 'vue'
import { cn } from '@/lib/utils'

interface SelectContentProps {
  class?: string
}

defineProps<SelectContentProps>()

const select = inject('select', {
  open: { value: false },
  modelValue: { value: '' },
  updateModelValue: () => {}
})

const open = computed(() => select.open.value)

const contentRef = ref<HTMLDivElement | null>(null)

// Close on click outside
function handleClickOutside(event: MouseEvent) {
  if (contentRef.value && !contentRef.value.contains(event.target as Node)) {
    select.open.value = false
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<template>
  <div
    v-if="open"
    ref="contentRef"
    :class="cn(
      'absolute z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md animate-in fade-in-80',
      'w-full mt-1 max-h-60 overflow-auto',
      $props.class
    )"
  >
    <div class="p-1">
      <slot></slot>
    </div>
  </div>
</template>
