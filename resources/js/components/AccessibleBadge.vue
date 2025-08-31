<template>
  <Badge
    :variant="variant"
    :class="badgeClasses"
    :role="role"
    :aria-label="ariaLabel"
    :aria-describedby="ariaDescribedBy"
    v-bind="$attrs"
  >
    <!-- Icon slot -->
    <Icon 
      v-if="icon" 
      :name="icon" 
      :class="iconClasses"
      aria-hidden="true"
    />
    
    <!-- Default slot for badge text -->
    <slot />
    
    <!-- Status indicator dot -->
    <span 
      v-if="showStatusDot" 
      :class="statusDotClasses"
      :aria-label="statusDotLabel"
      role="img"
    />
    
    <!-- Dismissible close button -->
    <button
      v-if="dismissible"
      type="button"
      :class="closeButtonClasses"
      :aria-label="closeButtonLabel"
      @click="handleDismiss"
    >
      <Icon name="X" class="w-3 h-3" aria-hidden="true" />
    </button>
  </Badge>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import Icon from '@/components/Icon.vue';
import { useTelegramTheme } from '@/composables/useTelegramTheme';
import { useTranslations } from '@/composables/useTranslations';

interface Props {
  variant?: 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning';
  size?: 'sm' | 'default' | 'lg';
  icon?: string;
  dismissible?: boolean;
  showStatusDot?: boolean;
  statusDotColor?: 'default' | 'success' | 'warning' | 'error';
  role?: string;
  ariaLabel?: string;
  ariaDescribedBy?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'default',
  dismissible: false,
  showStatusDot: false,
  statusDotColor: 'default',
  role: 'status'
});

const emit = defineEmits<{
  dismiss: [];
}>();

const { t } = useTranslations();
const { themeClasses } = useTelegramTheme();

// Dynamic styling with Telegram theme integration
const badgeClasses = computed(() => {
  const classes = ['inline-flex items-center gap-1 font-medium transition-colors'];
  
  // Size variants
  switch (props.size) {
    case 'sm':
      classes.push('text-xs px-1.5 py-0.5 rounded-md');
      break;
    case 'lg':
      classes.push('text-sm px-3 py-1.5 rounded-lg');
      break;
    default:
      classes.push('text-xs px-2 py-1 rounded-full');
  }
  
  // Enhanced variant styles with Telegram theme
  switch (props.variant) {
    case 'secondary':
      classes.push(
        `${themeClasses.value.background.secondary}`,
        `${themeClasses.value.text.secondary}`,
        `${themeClasses.value.border.separator}`,
        'border'
      );
      break;
    case 'destructive':
      classes.push(
        'bg-red-500/20 text-red-600 border border-red-500/30',
        'dark:bg-red-500/20 dark:text-red-400'
      );
      break;
    case 'outline':
      classes.push(
        'bg-transparent',
        `${themeClasses.value.text.primary}`,
        `${themeClasses.value.border.separator}`,
        'border'
      );
      break;
    case 'success':
      classes.push(
        'bg-green-500/20 text-green-600 border border-green-500/30',
        'dark:bg-green-500/20 dark:text-green-400'
      );
      break;
    case 'warning':
      classes.push(
        'bg-yellow-500/20 text-yellow-600 border border-yellow-500/30',
        'dark:bg-yellow-500/20 dark:text-yellow-400'
      );
      break;
    default:
      classes.push(
        `${themeClasses.value.interactive.accent}`,
        'text-tg-accent-foreground'
      );
  }
  
  return classes.join(' ');
});

const iconClasses = computed(() => {
  const baseClasses = 'shrink-0';
  
  switch (props.size) {
    case 'sm':
      return `${baseClasses} w-3 h-3`;
    case 'lg':
      return `${baseClasses} w-4 h-4`;
    default:
      return `${baseClasses} w-3 h-3`;
  }
});

const statusDotClasses = computed(() => {
  const baseClasses = 'inline-block w-2 h-2 rounded-full shrink-0';
  
  switch (props.statusDotColor) {
    case 'success':
      return `${baseClasses} bg-green-500`;
    case 'warning':
      return `${baseClasses} bg-yellow-500`;
    case 'error':
      return `${baseClasses} bg-red-500`;
    default:
      return `${baseClasses} bg-tg-accent`;
  }
});

const closeButtonClasses = computed(() => {
  return [
    'ml-1 -mr-1 shrink-0 rounded-full p-0.5',
    'transition-colors duration-200',
    'hover:bg-black/10 focus:bg-black/10',
    'dark:hover:bg-white/10 dark:focus:bg-white/10',
    'focus:outline-none focus:ring-1 focus:ring-tg-accent'
  ].join(' ');
});

// Accessibility labels
const statusDotLabel = computed(() => {
  switch (props.statusDotColor) {
    case 'success':
      return t('badge.status.success');
    case 'warning':
      return t('badge.status.warning');
    case 'error':
      return t('badge.status.error');
    default:
      return t('badge.status.active');
  }
});

const closeButtonLabel = computed(() => {
  return t('badge.dismiss', { default: 'Dismiss badge' });
});

// Event handlers
const handleDismiss = () => {
  emit('dismiss');
};
</script>

<style scoped>
/* Enhanced focus styles for close button */
.focus\:ring-1:focus {
  outline: none;
  box-shadow: 0 0 0 1px var(--tg-color-accent, #007aff);
}

/* Status indicator animations */
@keyframes pulse-dot {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse-dot {
  animation: pulse-dot 2s infinite;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-red-500\/30,
  .border-green-500\/30,
  .border-yellow-500\/30 {
    border-color: currentColor;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-colors,
  .animate-pulse-dot {
    transition: none;
    animation: none;
  }
}
</style>