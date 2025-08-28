<template>
  <Button v-bind="$attrs" @click="$emit('click')">
    <slot />
  </Button>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'

defineEmits<{
  click: []
}>()
</script>
