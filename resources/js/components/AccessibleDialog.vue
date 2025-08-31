<template>
  <Dialog v-model:open="isOpen" v-bind="$attrs">
    <DialogTrigger v-if="$slots.trigger" asChild>
      <slot name="trigger" />
    </DialogTrigger>
    
    <DialogContent
      :class="contentClasses"
      :aria-labelledby="titleId"
      :aria-describedby="descriptionId"
      @escape-key-down="handleEscape"
      @interact-outside="handleInteractOutside"
    >
      <!-- Close button with proper accessibility -->
      <button
        v-if="showCloseButton"
        type="button"
        :class="closeButtonClasses"
        :aria-label="closeButtonLabel"
        @click="handleClose"
      >
        <Icon name="X" class="w-4 h-4" aria-hidden="true" />
      </button>
      
      <!-- Header section -->
      <DialogHeader v-if="title || description || $slots.header" :class="headerClasses">
        <DialogTitle 
          v-if="title" 
          :id="titleId"
          :class="titleClasses"
        >
          {{ title }}
        </DialogTitle>
        
        <slot name="header" />
        
        <DialogDescription 
          v-if="description" 
          :id="descriptionId"
          :class="descriptionClasses"
        >
          {{ description }}
        </DialogDescription>
      </DialogHeader>
      
      <!-- Main content with proper focus management -->
      <div 
        :class="bodyClasses"
        :tabindex="hasScrollableContent ? 0 : undefined"
        :role="hasScrollableContent ? 'region' : undefined"
        :aria-label="hasScrollableContent ? scrollRegionLabel : undefined"
      >
        <slot />
      </div>
      
      <!-- Footer section -->
      <DialogFooter v-if="$slots.footer" :class="footerClasses">
        <slot name="footer" />
      </DialogFooter>
      
      <!-- Loading state overlay -->
      <div v-if="isLoading" :class="loadingOverlayClasses" role="status" :aria-label="loadingLabel">
        <Icon name="Loader2" class="w-6 h-6 animate-spin text-tg-accent" aria-hidden="true" />
        <span class="sr-only">{{ loadingLabel }}</span>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { computed, useId, watch, nextTick } from 'vue';
import { 
  Dialog, 
  DialogContent, 
  DialogDescription, 
  DialogFooter, 
  DialogHeader, 
  DialogTitle, 
  DialogTrigger 
} from '@/components/ui/dialog';
import Icon from '@/components/Icon.vue';
import { useTelegramTheme } from '@/composables/useTelegramTheme';
import { useTranslations } from '@/composables/useTranslations';

interface Props {
  open?: boolean;
  title?: string;
  description?: string;
  size?: 'sm' | 'default' | 'lg' | 'xl' | 'full';
  variant?: 'default' | 'destructive' | 'success';
  showCloseButton?: boolean;
  closeOnEscape?: boolean;
  closeOnOutsideClick?: boolean;
  isLoading?: boolean;
  hasScrollableContent?: boolean;
  preventClose?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  open: false,
  size: 'default',
  variant: 'default',
  showCloseButton: true,
  closeOnEscape: true,
  closeOnOutsideClick: true,
  isLoading: false,
  hasScrollableContent: false,
  preventClose: false
});

const emit = defineEmits<{
  'update:open': [value: boolean];
  close: [];
  escape: [];
  outsideClick: [];
}>();

const { t } = useTranslations();
const { themeClasses } = useTelegramTheme();

// Generate unique IDs for accessibility
const titleId = props.title ? useId() : undefined;
const descriptionId = props.description ? useId() : undefined;

// Reactive state
const isOpen = computed({
  get: () => props.open,
  set: (value) => emit('update:open', value)
});

// Dynamic styling with Telegram theme integration
const contentClasses = computed(() => {
  const classes = [
    `${themeClasses.value.background.card}`,
    `${themeClasses.value.border.separator}`,
    'border rounded-lg shadow-lg',
    'focus:outline-none'
  ];
  
  // Size variants
  switch (props.size) {
    case 'sm':
      classes.push('max-w-sm');
      break;
    case 'lg':
      classes.push('max-w-2xl');
      break;
    case 'xl':
      classes.push('max-w-4xl');
      break;
    case 'full':
      classes.push('max-w-[95vw] max-h-[95vh]');
      break;
    default:
      classes.push('max-w-md');
  }
  
  // Variant styles
  switch (props.variant) {
    case 'destructive':
      classes.push('border-red-500/50');
      break;
    case 'success':
      classes.push('border-green-500/50');
      break;
  }
  
  // Loading state
  if (props.isLoading) {
    classes.push('relative overflow-hidden');
  }
  
  return classes.join(' ');
});

const headerClasses = computed(() => {
  const classes = ['space-y-2'];
  
  // Variant-specific header styling
  if (props.variant === 'destructive') {
    classes.push('border-b border-red-500/20 pb-4');
  } else if (props.variant === 'success') {
    classes.push('border-b border-green-500/20 pb-4');
  }
  
  return classes.join(' ');
});

const titleClasses = computed(() => {
  const baseClasses = `${themeClasses.value.text.primary} text-lg font-semibold leading-tight`;
  
  switch (props.variant) {
    case 'destructive':
      return `${baseClasses} text-red-600 dark:text-red-400`;
    case 'success':
      return `${baseClasses} text-green-600 dark:text-green-400`;
    default:
      return baseClasses;
  }
});

const descriptionClasses = computed(() => {
  return `${themeClasses.value.text.secondary} text-sm leading-relaxed`;
});

const bodyClasses = computed(() => {
  const classes = ['py-4'];
  
  if (props.hasScrollableContent) {
    classes.push('overflow-y-auto max-h-[60vh] focus:outline-none focus:ring-2 focus:ring-tg-accent focus:ring-inset');
  }
  
  return classes.join(' ');
});

const footerClasses = computed(() => {
  return 'flex items-center justify-end gap-2 pt-4 border-t border-tg-section-separator';
});

const closeButtonClasses = computed(() => {
  return [
    'absolute right-4 top-4 rounded-sm opacity-70',
    'ring-offset-background transition-opacity',
    'hover:opacity-100 focus:outline-none focus:ring-2',
    'focus:ring-tg-accent focus:ring-offset-2',
    'disabled:pointer-events-none p-1'
  ].join(' ');
});

const loadingOverlayClasses = computed(() => {
  return [
    'absolute inset-0 bg-tg-bg/80 backdrop-blur-sm',
    'flex items-center justify-center',
    'z-50 rounded-lg'
  ].join(' ');
});

// Accessibility labels
const closeButtonLabel = computed(() => {
  return t('dialog.close', { default: 'Close dialog' });
});

const loadingLabel = computed(() => {
  return t('dialog.loading', { default: 'Loading...' });
});

const scrollRegionLabel = computed(() => {
  return t('dialog.scroll_content', { default: 'Scrollable content' });
});

// Event handlers
const handleClose = () => {
  if (!props.preventClose) {
    emit('close');
    isOpen.value = false;
  }
};

const handleEscape = () => {
  emit('escape');
  if (props.closeOnEscape && !props.preventClose) {
    handleClose();
  }
};

const handleInteractOutside = () => {
  emit('outsideClick');
  if (props.closeOnOutsideClick && !props.preventClose) {
    handleClose();
  }
};

// Focus management
watch(isOpen, async (newValue) => {
  if (newValue) {
    await nextTick();
    // Focus the first focusable element or the content area
    const content = document.querySelector('[data-dialog-content]');
    const firstFocusable = content?.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])') as HTMLElement;
    
    if (firstFocusable) {
      firstFocusable.focus();
    }
  }
});
</script>

<style scoped>
/* Enhanced focus styles */
.focus\:ring-2:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--tg-color-accent, #007aff);
}

.focus\:ring-inset:focus {
  box-shadow: inset 0 0 0 2px var(--tg-color-accent, #007aff);
}

/* Backdrop blur support */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

/* Loading animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Screen reader only text */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-red-500\/50,
  .border-green-500\/50,
  .border-tg-section-separator {
    border-color: currentColor;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-opacity,
  .animate-spin {
    transition: none;
    animation: none;
  }
}
</style>