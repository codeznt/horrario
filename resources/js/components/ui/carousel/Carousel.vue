<script setup lang="ts">
import { computed, onMounted, provide, ref, watch } from 'vue'
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import emblaCarouselVue from 'embla-carousel-vue'
import type { EmblaCarouselType } from 'embla-carousel'

interface CarouselProps {
  class?: HTMLAttributes['class']
  orientation?: 'horizontal' | 'vertical'
  opts?: Parameters<typeof emblaCarouselVue>[0]
  plugins?: Parameters<typeof emblaCarouselVue>[1]
}

const props = withDefaults(defineProps<CarouselProps>(), {
  orientation: 'horizontal',
  opts: () => ({})
})

const emit = defineEmits<{
  'init-api': [api: EmblaCarouselType]
  'slide-changed': [index: number]
}>()

// Embla Carousel setup
const [emblaRef, emblaApi] = emblaCarouselVue(
  () => ({
    ...props.opts,
    axis: props.orientation === 'vertical' ? 'y' : 'x',
  }),
  props.plugins
)

// Carousel state
const canScrollPrev = ref(false)
const canScrollNext = ref(false)
const selectedIndex = ref(0)

function scrollPrev() {
  emblaApi.value?.scrollPrev()
}

function scrollNext() {
  emblaApi.value?.scrollNext()
}

function scrollTo(index: number) {
  emblaApi.value?.scrollTo(index)
}

function onInit(api: EmblaCarouselType) {
  emit('init-api', api)
}

function onSelect(api: EmblaCarouselType) {
  selectedIndex.value = api.selectedScrollSnap()
  canScrollPrev.value = api.canScrollPrev()
  canScrollNext.value = api.canScrollNext()
  emit('slide-changed', selectedIndex.value)
}

watch(emblaApi, (api) => {
  if (!api) return

  onInit(api)
  onSelect(api)

  api.on('reInit', onInit)
  api.on('reInit', onSelect)
  api.on('select', onSelect)
}, { immediate: true })

// Provide carousel context
provide('carousel', {
  carouselRef: emblaRef,
  carouselApi: emblaApi,
  canScrollPrev,
  canScrollNext,
  scrollPrev,
  scrollNext,
  scrollTo,
  orientation: props.orientation
})
</script>

<template>
  <div 
    :class="cn('relative', props.class)" 
    role="region" 
    aria-roledescription="carousel"
  >
    <div ref="emblaRef" class="overflow-hidden">
      <slot />
    </div>
  </div>
</template>