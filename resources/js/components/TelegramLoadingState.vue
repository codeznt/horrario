<template>
  <div 
    v-if="show"
    :class="containerClasses"
    :style="containerStyles"
  >
    <div :class="contentClasses">
      <!-- Loading icon -->
      <div :class="iconContainerClasses">
        <Icon 
          name="Loader2" 
          :class="iconClasses"
        />
      </div>
      
      <!-- Loading message -->
      <div v-if="message" :class="messageClasses">
        {{ message }}
      </div>
      
      <!-- Optional description -->
      <div v-if="description" :class="descriptionClasses">
        {{ description }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Icon from '@/components/Icon.vue';
import { useTelegramTheme } from '@/composables/useTelegramTheme';

interface Props {
  show: boolean;
  message?: string;
  description?: string;
  overlay?: boolean;
  size?: 'sm' | 'md' | 'lg';
  variant?: 'default' | 'minimal' | 'card';
}

const props = withDefaults(defineProps<Props>(), {
  overlay: false,
  size: 'md',
  variant: 'default'
});

const { themeClasses, navigationAwareStyles } = useTelegramTheme();

// Dynamic classes based on props and theme
const containerClasses = computed(() => {
  const base = 'flex items-center justify-center transition-opacity duration-200';
  
  if (props.overlay) {
    return `${base} fixed inset-0 z-50 bg-tg-bg/80 backdrop-blur-sm`;
  }
  
  return `${base} w-full py-8`;
});

const containerStyles = computed(() => {
  if (props.overlay) {
    return {
      ...navigationAwareStyles.value
    };
  }
  return {};
});

const contentClasses = computed(() => {
  const base = 'flex flex-col items-center gap-3 text-center';
  
  switch (props.variant) {
    case 'card':
      return `${base} ${themeClasses.value.background.card} ${themeClasses.value.border.separator} border rounded-lg p-6 min-w-48 shadow-sm`;
    case 'minimal':
      return `${base} p-2`;
    default:
      return `${base} ${themeClasses.value.background.secondary} rounded-lg p-4 min-w-48`;
  }
});

const iconContainerClasses = computed(() => {
  const base = 'flex items-center justify-center';
  
  switch (props.size) {
    case 'sm':
      return `${base} w-6 h-6`;
    case 'lg':
      return `${base} w-12 h-12`;
    default:
      return `${base} w-8 h-8`;
  }
});

const iconClasses = computed(() => {
  const base = `${themeClasses.value.text.accent} animate-spin`;
  
  switch (props.size) {
    case 'sm':
      return `${base} w-4 h-4`;
    case 'lg':
      return `${base} w-8 h-8`;
    default:
      return `${base} w-6 h-6`;
  }
});

const messageClasses = computed(() => {
  const base = `${themeClasses.value.text.primary} font-medium`;
  
  switch (props.size) {
    case 'sm':
      return `${base} text-sm`;
    case 'lg':
      return `${base} text-lg`;
    default:
      return `${base} text-base`;
  }
});

const descriptionClasses = computed(() => {
  return `${themeClasses.value.text.secondary} text-sm max-w-xs`;
});
</script>

<style scoped>
/* Ensure smooth animations with Telegram theme integration */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Backdrop blur support */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

/* Safe area awareness */
@supports (padding: max(0px)) {
  .fixed.inset-0 {
    padding-top: max(var(--tma-safe-top), 0px);
    padding-bottom: max(var(--tma-safe-bottom), 0px);
  }
}
</style>