<template>
  <Button
    :variant="variant"
    :size="size"
    :disabled="disabled || isLoading"
    :class="buttonClasses"
    :aria-label="ariaLabel"
    :aria-describedby="ariaDescribedBy"
    :aria-pressed="pressed"
    :aria-expanded="expanded"
    :type="type"
    v-bind="$attrs"
    @click="handleClick"
  >
    <!-- Loading state -->
    <Icon 
      v-if="isLoading" 
      name="Loader2" 
      class="w-4 h-4 animate-spin mr-2" 
      aria-hidden="true"
    />
    
    <!-- Icon slot -->
    <Icon 
      v-else-if="icon && iconPosition === 'left'" 
      :name="icon" 
      class="w-4 h-4 mr-2" 
      aria-hidden="true"
    />
    
    <!-- Default slot for button text -->
    <slot />
    
    <!-- Right icon -->
    <Icon 
      v-if="icon && iconPosition === 'right' && !isLoading" 
      :name="icon" 
      class="w-4 h-4 ml-2" 
      aria-hidden="true"
    />
    
    <!-- External link indicator -->
    <Icon 
      v-if="external" 
      name="ExternalLink" 
      class="w-3 h-3 ml-1" 
      aria-hidden="true"
    />
  </Button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useTelegramTheme } from '@/composables/useTelegramTheme';

interface Props {
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  disabled?: boolean;
  isLoading?: boolean;
  icon?: string;
  iconPosition?: 'left' | 'right';
  external?: boolean;
  ariaLabel?: string;
  ariaDescribedBy?: string;
  pressed?: boolean;
  expanded?: boolean;
  type?: 'button' | 'submit' | 'reset';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'default',
  disabled: false,
  isLoading: false,
  iconPosition: 'left',
  external: false,
  type: 'button'
});

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const { themeClasses } = useTelegramTheme();

const buttonClasses = computed(() => {
  const classes = [];
  
  // Add focus styles for keyboard navigation
  classes.push('focus-visible:ring-2 focus-visible:ring-tg-accent focus-visible:ring-offset-2');
  
  // Add hover states using Telegram theme
  if (!props.disabled && !props.isLoading) {
    classes.push('transition-colors duration-200');
    
    switch (props.variant) {
      case 'default':
        classes.push('hover:bg-tg-accent/90');
        break;
      case 'outline':
        classes.push(`hover:${themeClasses.value.background.secondary}`);
        break;
      case 'ghost':
        classes.push(`hover:${themeClasses.value.background.secondary}`);
        break;
    }
  }
  
  // Disabled styles
  if (props.disabled || props.isLoading) {
    classes.push('opacity-50 cursor-not-allowed');
  }
  
  return classes.join(' ');
});

const handleClick = (event: MouseEvent) => {
  if (props.disabled || props.isLoading) {
    event.preventDefault();
    return;
  }
  emit('click', event);
};
</script>

<style scoped>
/* Enhanced focus styles for better accessibility */
.focus-visible\:ring-2:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px var(--tg-color-accent, #007aff);
}

/* Ensure proper contrast for disabled state */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Loading state animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>