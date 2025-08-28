<script setup lang="ts">
import { inject, computed } from 'vue'
import { cn } from '@/lib/utils'
import Icon from '@/components/Icon.vue'

interface SelectItemProps {
  value: string
  class?: string
}

const props = defineProps<SelectItemProps>()

const select = inject('select', {
  modelValue: { value: '' },
  updateModelValue: (value: string) => {}
})

const isSelected = computed(() => select.modelValue.value === props.value)

function handleSelect() {
  select.updateModelValue(props.value)
}
</script>

<template>
  <div
    role="option"
    :class="cn(
      'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none',
      isSelected ? 'bg-accent text-accent-foreground' : 'hover:bg-accent hover:text-accent-foreground',
      $props.class
    )"
    :aria-selected="isSelected"
    @click="handleSelect"
  >
    <Icon v-if="isSelected" name="Check" class="mr-2 h-4 w-4" />
    <span v-else class="mr-2 h-4 w-4"></span>
    <slot></slot>
  </div>
</template>
