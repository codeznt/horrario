<script setup lang="ts">
import { computed, provide, ref, watch, type Ref } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface StepperProps {
  class?: HTMLAttributes['class']
  modelValue?: number
  orientation?: 'horizontal' | 'vertical'
}

interface StepperContext {
  modelValue: Ref<number>
  changeStep: (step: number) => void
  orientation: 'horizontal' | 'vertical'
  isClickable: boolean
}

const props = withDefaults(defineProps<StepperProps>(), {
  modelValue: 1,
  orientation: 'horizontal'
})

const emit = defineEmits<{
  'update:modelValue': [value: number]
  'step-change': [step: number]
}>()

const currentStep = ref(props.modelValue)

const changeStep = (step: number) => {
  currentStep.value = step
  emit('update:modelValue', step)
  emit('step-change', step)
}

// Provide stepper context
provide<StepperContext>('stepper', {
  modelValue: currentStep,
  changeStep,
  orientation: props.orientation,
  isClickable: true
})

// Watch for external modelValue changes
watch(() => props.modelValue, (newValue) => {
  currentStep.value = newValue
}, { immediate: true })
</script>

<template>
  <div
    :class="cn(
      'flex',
      props.orientation === 'vertical' ? 'flex-col space-y-4' : 'items-center space-x-4',
      props.class
    )"
    role="tablist"
    :aria-orientation="props.orientation"
  >
    <slot />
  </div>
</template>