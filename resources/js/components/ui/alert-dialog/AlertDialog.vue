<template>
  <DialogRoot :open="open" @update:open="$emit('update:open', $event)">
    <slot />
  </DialogRoot>
</template>

<script setup lang="ts">
import { DialogRoot } from 'reka-ui'

defineEmits<{
  'update:open': [value: boolean]
}>()

defineProps<{
  open?: boolean
}>()
</script>
