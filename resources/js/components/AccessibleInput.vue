<template>
  <div class="space-y-2">
    <!-- Label with proper association -->
    <Label 
      v-if="label" 
      :for="inputId"
      :class="labelClasses"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1" aria-label="required">*</span>
    </Label>
    
    <!-- Input wrapper for enhanced styling -->
    <div class="relative">
      <Input
        :id="inputId"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :class="inputClasses"
        :aria-describedby="ariaDescribedBy"
        :aria-invalid="hasError"
        :aria-required="required"
        v-model="modelValue"
        v-bind="$attrs"
        @blur="handleBlur"
        @focus="handleFocus"
      />
      
      <!-- Icon indicator -->
      <div v-if="icon || hasError" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <Icon 
          v-if="hasError" 
          name="AlertCircle" 
          class="w-4 h-4 text-red-500" 
          :aria-label="t('app.error_indicator')"
        />
        <Icon 
          v-else-if="icon" 
          :name="icon" 
          class="w-4 h-4 text-tg-hint" 
          aria-hidden="true"
        />
      </div>
      
      <!-- Loading indicator -->
      <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <Icon 
          name="Loader2" 
          class="w-4 h-4 animate-spin text-tg-accent" 
          :aria-label="t('app.loading')"
        />
      </div>
    </div>
    
    <!-- Help text -->
    <p 
      v-if="helpText && !hasError" 
      :id="`${inputId}-help`"
      :class="helpTextClasses"
    >
      {{ helpText }}
    </p>
    
    <!-- Error message -->
    <p 
      v-if="hasError && errorMessage" 
      :id="`${inputId}-error`"
      :class="errorMessageClasses"
      role="alert"
      aria-live="polite"
    >
      {{ errorMessage }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, useId } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Icon from '@/components/Icon.vue';
import { useTelegramTheme } from '@/composables/useTelegramTheme';
import { useTranslations } from '@/composables/useTranslations';

interface Props {
  modelValue?: string | number;
  label?: string;
  placeholder?: string;
  helpText?: string;
  errorMessage?: string;
  type?: string;
  disabled?: boolean;
  readonly?: boolean;
  required?: boolean;
  isLoading?: boolean;
  icon?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  disabled: false,
  readonly: false,
  required: false,
  isLoading: false
});

const emit = defineEmits<{
  'update:modelValue': [value: string | number];
  blur: [event: FocusEvent];
  focus: [event: FocusEvent];
}>();

const { t } = useTranslations();
const { themeClasses } = useTelegramTheme();

// Generate unique ID for accessibility
const inputId = useId();

// Compute derived state
const hasError = computed(() => !!props.errorMessage);
const isFocused = ref(false);

const ariaDescribedBy = computed(() => {
  const ids = [];
  if (props.helpText && !hasError.value) {
    ids.push(`${inputId}-help`);
  }
  if (hasError.value) {
    ids.push(`${inputId}-error`);
  }
  return ids.length > 0 ? ids.join(' ') : undefined;
});

// Styling with Telegram theme integration
const labelClasses = computed(() => {
  return `${themeClasses.value.text.primary} text-sm font-medium`;
});

const inputClasses = computed(() => {
  const baseClasses = [
    'transition-colors duration-200',
    'focus:ring-2 focus:ring-tg-accent focus:ring-offset-1',
    'placeholder:text-tg-hint'
  ];
  
  if (hasError.value) {
    baseClasses.push('border-red-500 focus:border-red-500 focus:ring-red-500');
  } else {
    baseClasses.push('border-tg-section-separator focus:border-tg-accent');
  }
  
  if (props.disabled) {
    baseClasses.push('opacity-50 cursor-not-allowed bg-tg-section-bg/50');
  } else {
    baseClasses.push('bg-tg-section-bg hover:bg-tg-secondary-bg');
  }
  
  // Add padding for icons
  if (props.icon || hasError.value || props.isLoading) {
    baseClasses.push('pr-10');
  }
  
  return baseClasses.join(' ');
});

const helpTextClasses = computed(() => {
  return `${themeClasses.value.text.secondary} text-sm`;
});

const errorMessageClasses = computed(() => {
  return 'text-red-600 text-sm font-medium';
});

// Event handlers
const handleFocus = (event: FocusEvent) => {
  isFocused.value = true;
  emit('focus', event);
};

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false;
  emit('blur', event);
};

// Model value handling
const modelValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});
</script>

<style scoped>
/* Enhanced focus styles */
.focus\:ring-2:focus {
  outline: none;
  box-shadow: 0 0 0 2px var(--tg-color-accent, #007aff);
}

/* Smooth transitions for better UX */
input {
  transition: border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
}

/* Loading animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-tg-section-separator {
    border-color: currentColor;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  input, .animate-spin {
    transition: none;
    animation: none;
  }
}
</style>