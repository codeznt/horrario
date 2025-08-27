<script setup lang="ts">
import { inject } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface CarouselContentProps {
  class?: HTMLAttributes['class']
}

const props = defineProps<CarouselContentProps>()

const carousel = inject<any>('carousel')
</script>

<template>
  <div
    :class="cn(
      'flex',
      carousel?.orientation === 'vertical' ? 'flex-col' : 'flex-row',
      props.class
    )"
  >
    <slot />
  </div>
</template>