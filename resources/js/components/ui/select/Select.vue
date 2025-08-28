<script setup lang="ts">
import { ref, provide } from 'vue'

const modelValue = defineModel<string>()
const open = ref(false)

provide('select', {
  open,
  modelValue,
  updateModelValue: (value: string) => {
    modelValue.value = value
    open.value = false
  }
})
</script>

<template>
  <div class="relative">
    <slot></slot>
  </div>
</template>
