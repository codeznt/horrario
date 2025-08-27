<script setup lang="ts">
import { inject } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

interface CarouselItemProps {
  class?: HTMLAttributes['class']
}

const props = defineProps<CarouselItemProps>()

const carousel = inject<any>('carousel')
</script>

<template>
  <div
    :class="cn(
      'min-w-0 shrink-0 grow-0 basis-full',
      carousel?.orientation === 'vertical' ? 'pb-4' : 'pl-4',
      props.class
    )"
    role="group"
    aria-roledescription="slide"
  >
    <slot />
  </div>
</template>