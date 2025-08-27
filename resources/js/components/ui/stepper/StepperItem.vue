<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface StepperItemProps {
  class?: HTMLAttributes['class']
  step: number
}

const props = defineProps<StepperItemProps>()

const stepper = inject<any>('stepper')

const isActive = computed(() => stepper?.modelValue.value === props.step)
const isCompleted = computed(() => stepper?.modelValue.value > props.step)
</script>

<template>
  <div
    :class="cn(
      'flex items-center',
      stepper?.orientation === 'vertical' ? 'flex-col' : '',
      props.class
    )"
  >
    <slot :is-active="isActive" :is-completed="isCompleted" />
  </div>
</template>