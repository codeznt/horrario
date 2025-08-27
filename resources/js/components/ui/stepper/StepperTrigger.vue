<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface StepperTriggerProps {
  class?: HTMLAttributes['class']
  step: number
  disabled?: boolean
}

const props = withDefaults(defineProps<StepperTriggerProps>(), {
  disabled: false
})

const stepper = inject<any>('stepper')

const isActive = computed(() => stepper?.modelValue.value === props.step)
const isCompleted = computed(() => stepper?.modelValue.value > props.step)

const handleClick = () => {
  if (!props.disabled && stepper?.isClickable) {
    stepper?.changeStep(props.step)
  }
}
</script>

<template>
  <button
    type="button"
    :class="cn(
      'flex items-center justify-center transition-all duration-200',
      !props.disabled && stepper?.isClickable && 'cursor-pointer hover:opacity-75',
      props.disabled && 'cursor-not-allowed opacity-50',
      props.class
    )"
    :disabled="props.disabled"
    :aria-selected="isActive"
    :aria-disabled="props.disabled"
    role="tab"
    @click="handleClick"
  >
    <slot :is-active="isActive" :is-completed="isCompleted" />
  </button>
</template>