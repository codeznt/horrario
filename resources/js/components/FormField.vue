<template>
  <div class="form-field mb-4">
    <Label v-if="label" :for="id" class="block text-sm font-medium mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </Label>
    
    <div class="relative">
      <slot />
    </div>
    
    <p v-if="error" class="text-red-500 text-sm mt-1 flex items-center gap-1">
      <Icon name="AlertCircle" class="h-3 w-3" />
      {{ error }}
    </p>
    <p v-else-if="hint" class="text-tg-hint text-sm mt-1">
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { Label } from '@/components/ui'
import Icon from '@/components/Icon.vue'

interface Props {
  id?: string
  label?: string
  error?: string
  hint?: string
  required?: boolean
}

withDefaults(defineProps<Props>(), {
  required: false,
})
</script>
