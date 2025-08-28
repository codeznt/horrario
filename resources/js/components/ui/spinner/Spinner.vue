<template>
  <div :class="spinnerClasses">
    <svg
      class="animate-spin"
      :width="sizeValue"
      :height="sizeValue"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <circle
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
        class="opacity-25"
      />
      <path
        d="m12 2a10 10 0 0 1 10 10h-4a6 6 0 0 0-6-6z"
        fill="currentColor"
      />
    </svg>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  size?: 'sm' | 'md' | 'lg' | 'xl'
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
})

const sizeValue = computed(() => {
  const sizes = {
    sm: 16,
    md: 24,
    lg: 32,
    xl: 48,
  }
  return sizes[props.size]
})

const spinnerClasses = computed(() =>
  cn(
    'inline-flex items-center justify-center',
    'text-tg-accent',
    props.class
  )
)
</script>
