<template>
  <Link 
    :href="href"
    :class="[
      'nav-link flex flex-col items-center justify-center p-3 min-w-0 flex-1',
      'transition-colors duration-200',
      active ? 'text-tg-link' : 'text-tg-hint hover:text-tg-text'
    ]"
  >
    <Icon :name="icon" class="h-5 w-5 mb-1" />
    <span class="text-xs font-medium truncate">{{ label }}</span>
  </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Icon from '@/components/Icon.vue'

interface Props {
  href: string
  active: boolean
  icon: string
  label: string
}

defineProps<Props>()
</script>

<style scoped>
.nav-link {
  min-height: 60px;
}

/* Ensure proper touch targets on mobile */
@media (max-width: 640px) {
  .nav-link {
    min-height: 64px;
  }
}
</style>
