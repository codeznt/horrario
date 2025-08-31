<template>
  <Card
    :class="cardClasses"
    :role="role"
    :aria-labelledby="ariaLabelledBy"
    :aria-describedby="ariaDescribedBy"
    :tabindex="interactive ? 0 : undefined"
    v-bind="$attrs"
    @click="handleClick"
    @keydown="handleKeydown"
  >
    <!-- Header section with proper heading structure -->
    <CardHeader v-if="title || $slots.header" :class="headerClasses">
      <CardTitle 
        v-if="title" 
        :id="titleId"
        :class="titleClasses"
      >
        {{ title }}
      </CardTitle>
      <slot name="header" />
      
      <CardDescription 
        v-if="description" 
        :id="descriptionId"
        :class="descriptionClasses"
      >
        {{ description }}
      </CardDescription>
    </CardHeader>
    
    <!-- Main content -->
    <CardContent :class="contentClasses">
      <slot />
    </CardContent>
    
    <!-- Footer section -->
    <CardFooter v-if="$slots.footer" :class="footerClasses">
      <slot name="footer" />
    </CardFooter>
  </Card>
</template>

<script setup lang="ts">
import { computed, useId } from 'vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { useTelegramTheme } from '@/composables/useTelegramTheme';

interface Props {
  title?: string;
  description?: string;
  interactive?: boolean;
  elevated?: boolean;
  variant?: 'default' | 'elevated' | 'outline' | 'ghost';
  role?: string;
  ariaLabelledBy?: string;
  ariaDescribedBy?: string;
}

const props = withDefaults(defineProps<Props>(), {
  interactive: false,
  elevated: false,
  variant: 'default',
  role: 'region'
});

const emit = defineEmits<{
  click: [event: MouseEvent];
  activate: [event: KeyboardEvent | MouseEvent];
}>();

const { themeClasses } = useTelegramTheme();

// Generate unique IDs for accessibility
const titleId = props.title ? useId() : undefined;
const descriptionId = props.description ? useId() : undefined;

// Compute ARIA attributes
const ariaLabelledBy = computed(() => {
  const ids = [];
  if (props.ariaLabelledBy) {
    ids.push(props.ariaLabelledBy);
  } else if (titleId) {
    ids.push(titleId);
  }
  return ids.length > 0 ? ids.join(' ') : undefined;
});

const ariaDescribedBy = computed(() => {
  const ids = [];
  if (props.ariaDescribedBy) {
    ids.push(props.ariaDescribedBy);
  } else if (descriptionId) {
    ids.push(descriptionId);
  }
  return ids.length > 0 ? ids.join(' ') : undefined;
});

// Dynamic styling with Telegram theme integration
const cardClasses = computed(() => {
  const classes = ['transition-colors duration-200'];
  
  // Variant styles
  switch (props.variant) {
    case 'elevated':
      classes.push(
        `${themeClasses.value.background.card}`,
        'shadow-lg border-0'
      );
      break;
    case 'outline':
      classes.push(
        `${themeClasses.value.background.primary}`,
        `${themeClasses.value.border.separator}`,
        'border-2'
      );
      break;
    case 'ghost':
      classes.push(
        'bg-transparent border-0'
      );
      break;
    default:
      classes.push(
        `${themeClasses.value.background.card}`,
        `${themeClasses.value.border.separator}`,
        'border'
      );
  }
  
  // Interactive states
  if (props.interactive) {
    classes.push(
      'cursor-pointer',
      'focus:outline-none focus:ring-2 focus:ring-tg-accent focus:ring-offset-2',
      `hover:${themeClasses.value.background.secondary}`,
      'active:scale-[0.98]'
    );
  }
  
  return classes.join(' ');
});

const headerClasses = computed(() => {
  return 'space-y-1.5';
});

const titleClasses = computed(() => {
  return `${themeClasses.value.text.primary} text-lg font-semibold leading-tight`;
});

const descriptionClasses = computed(() => {
  return `${themeClasses.value.text.secondary} text-sm leading-relaxed`;
});

const contentClasses = computed(() => {
  return 'space-y-4';
});

const footerClasses = computed(() => {
  return 'pt-4 border-t border-tg-section-separator';
});

// Event handlers
const handleClick = (event: MouseEvent) => {
  if (props.interactive) {
    emit('click', event);
    emit('activate', event);
  }
};

const handleKeydown = (event: KeyboardEvent) => {
  if (props.interactive && (event.key === 'Enter' || event.key === ' ')) {
    event.preventDefault();
    emit('activate', event);
  }
};
</script>

<style scoped>
/* Enhanced focus styles for accessibility */
.focus\:ring-2:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--tg-color-accent, #007aff);
}

/* Smooth hover transitions */
.hover\:bg-tg-secondary-bg:hover {
  background-color: var(--tg-color-secondary-bg, #f1f1f1);
}

/* Active state animation */
.active\:scale-\[0\.98\]:active {
  transform: scale(0.98);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-tg-section-separator {
    border-color: currentColor;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-colors,
  .active\:scale-\[0\.98\]:active {
    transition: none;
    transform: none;
  }
}
</style>