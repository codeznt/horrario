<script setup lang="ts">
import { inject, computed } from 'vue'
import type { HTMLAttributes } from 'vue'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'
import { ChevronRight } from 'lucide-vue-next'

interface CarouselNextProps {
  class?: HTMLAttributes['class']
  variant?: 'default' | 'outline' | 'ghost'
  size?: 'default' | 'sm' | 'lg' | 'icon'
}

const props = withDefaults(defineProps<CarouselNextProps>(), {
  variant: 'outline',
  size: 'icon'
})

const carousel = inject<any>('carousel')

const isDisabled = computed(() => !carousel?.canScrollNext?.value)
</script>

<template>
  <Button
    :class="cn(
      'absolute h-8 w-8 rounded-full',
      carousel?.orientation === 'vertical' 
        ? '-bottom-12 left-1/2 -translate-x-1/2 rotate-90' 
        : '-right-12 top-1/2 -translate-y-1/2',
      props.class
    )"
    :variant="props.variant"
    :size="props.size"
    :disabled="isDisabled"
    @click="carousel?.scrollNext"
  >
    <ChevronRight class="h-4 w-4" />
    <span class="sr-only">Next slide</span>
  </Button>
</template>