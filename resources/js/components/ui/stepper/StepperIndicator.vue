<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes, Component } from 'vue'
import { cn } from '@/lib/utils'
import { Check } from 'lucide-vue-next'

interface StepperIndicatorProps {
  class?: HTMLAttributes['class']
  step: number
  icon?: Component
}

const props = defineProps<StepperIndicatorProps>()

const stepper = inject<any>('stepper')

const isActive = computed(() => stepper?.modelValue.value === props.step)
const isCompleted = computed(() => stepper?.modelValue.value > props.step)
</script>

<template>
  <div
    :class="cn(
      'flex h-8 w-8 items-center justify-center rounded-full border-2 text-sm font-medium transition-all duration-200',
      isCompleted && 'border-tg-accent bg-tg-accent text-white',
      isActive && !isCompleted && 'border-tg-accent bg-tg-accent/10 text-tg-accent',
      !isActive && !isCompleted && 'border-tg-section-separator bg-tg-section-bg text-tg-subtitle-text',
      props.class
    )"
  >
    <Check v-if="isCompleted" class="h-4 w-4" />
    <component v-else-if="props.icon" :is="props.icon" class="h-4 w-4" />
    <span v-else>{{ props.step }}</span>
  </div>
</template>