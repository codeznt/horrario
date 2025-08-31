import { computed, ref } from 'vue';

/**
 * Centralized Telegram theme management
 * Provides standardized access to Telegram theme variables and utilities
 */
export function useTelegramTheme() {
  // Theme state - reactive to theme changes
  const isDarkMode = ref(false);
  
  // Watch for theme changes in CSS variables
  const updateThemeState = () => {
    if (typeof window !== 'undefined') {
      const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--tg-color-scheme');
      isDarkMode.value = bgColor === 'dark';
    }
  };

  // Theme CSS classes for Telegram integration
  const themeClasses = computed(() => ({
    // Background colors using Telegram theme variables
    background: {
      primary: 'bg-tg-bg',
      secondary: 'bg-tg-secondary-bg',
      section: 'bg-tg-section-bg',
      header: 'bg-tg-secondary-bg',
      card: 'bg-tg-section-bg',
    },
    
    // Text colors
    text: {
      primary: 'text-tg-text',
      secondary: 'text-tg-subtitle-text',
      hint: 'text-tg-hint',
      link: 'text-tg-link',
      accent: 'text-tg-accent',
    },
    
    // Border colors
    border: {
      separator: 'border-tg-section-separator',
      accent: 'border-tg-accent',
    },
    
    // Interactive elements
    interactive: {
      accent: 'bg-tg-accent text-tg-accent-foreground',
      accentHover: 'hover:bg-tg-accent/90',
      button: 'bg-tg-button text-tg-button-text',
      buttonHover: 'hover:bg-tg-button/90',
      destructive: 'bg-red-500/20 text-red-600 border-red-500/30',
      success: 'bg-green-500/20 text-green-600 border-green-500/30',
      warning: 'bg-yellow-500/20 text-yellow-600 border-yellow-500/30',
    },
    
    // Layout utilities
    layout: {
      padded: 'p-4',
      spacing: 'gap-4',
      rounded: 'rounded-lg',
      shadow: 'shadow-sm',
    }
  }));

  // Standardized component variants using Telegram theme
  const componentVariants = {
    button: {
      primary: `${themeClasses.value.interactive.accent} ${themeClasses.value.interactive.accentHover} ${themeClasses.value.layout.rounded} ${themeClasses.value.layout.padded}`,
      secondary: `${themeClasses.value.background.secondary} ${themeClasses.value.text.primary} ${themeClasses.value.border.separator} border ${themeClasses.value.layout.rounded} ${themeClasses.value.layout.padded}`,
      ghost: `${themeClasses.value.text.primary} hover:${themeClasses.value.background.secondary} ${themeClasses.value.layout.rounded} ${themeClasses.value.layout.padded}`,
      destructive: `${themeClasses.value.interactive.destructive} ${themeClasses.value.layout.rounded} ${themeClasses.value.layout.padded} border`,
    },
    
    card: {
      default: `${themeClasses.value.background.card} ${themeClasses.value.border.separator} border ${themeClasses.value.layout.rounded} ${themeClasses.value.layout.shadow}`,
      elevated: `${themeClasses.value.background.card} ${themeClasses.value.layout.rounded} shadow-md`,
      interactive: `${themeClasses.value.background.card} ${themeClasses.value.border.separator} border ${themeClasses.value.layout.rounded} transition-colors hover:${themeClasses.value.background.secondary}`,
    },
    
    input: {
      default: `${themeClasses.value.background.section} ${themeClasses.value.text.primary} ${themeClasses.value.border.separator} border ${themeClasses.value.layout.rounded} focus:${themeClasses.value.border.accent}`,
      error: `${themeClasses.value.background.section} ${themeClasses.value.text.primary} border-red-500 ${themeClasses.value.layout.rounded}`,
    },
    
    badge: {
      default: `${themeClasses.value.interactive.accent} text-xs font-medium px-2 py-1 rounded-full`,
      secondary: `${themeClasses.value.background.secondary} ${themeClasses.value.text.secondary} text-xs font-medium px-2 py-1 rounded-full`,
      success: `${themeClasses.value.interactive.success} text-xs font-medium px-2 py-1 rounded-full border`,
      warning: `${themeClasses.value.interactive.warning} text-xs font-medium px-2 py-1 rounded-full border`,
      destructive: `${themeClasses.value.interactive.destructive} text-xs font-medium px-2 py-1 rounded-full border`,
    }
  };

  // CSS custom properties for dynamic theming
  const telegramCSSVariables = {
    // Main colors
    '--app-bg': 'var(--tg-color-bg, var(--background))',
    '--app-text': 'var(--tg-color-text, var(--foreground))',
    '--app-accent': 'var(--tg-color-accent, var(--accent))',
    
    // Component-specific
    '--button-bg': 'var(--tg-color-button, var(--app-accent))',
    '--button-text': 'var(--tg-color-button-text, white)',
    '--card-bg': 'var(--tg-color-section-bg, var(--card))',
    '--border-color': 'var(--tg-color-section-separator, var(--border))',
  };

  // Helper to apply CSS variables
  const applyCSSVariables = () => {
    if (typeof window !== 'undefined') {
      Object.entries(telegramCSSVariables).forEach(([property, value]) => {
        document.documentElement.style.setProperty(property, value);
      });
    }
  };

  // Safe area utilities for Telegram Mini Apps
  const safeAreaStyles = computed(() => ({
    paddingTop: 'max(var(--tma-safe-top), 0px)',
    paddingBottom: 'max(var(--tma-safe-bottom), 0px)',
    paddingLeft: 'max(var(--tma-safe-left), 0px)',
    paddingRight: 'max(var(--tma-safe-right), 0px)',
  }));

  // Navigation bar height consideration
  const navigationAwareStyles = computed(() => ({
    paddingBottom: '80px', // Space for bottom navigation
    minHeight: 'var(--tma-viewport-h, 100vh)',
  }));

  // Responsive design utilities
  const responsive = {
    mobile: 'max-w-sm mx-auto',
    tablet: 'max-w-md mx-auto',
    desktop: 'max-w-lg mx-auto',
    fullWidth: 'w-full',
  };

  // Initialize theme detection
  if (typeof window !== 'undefined') {
    updateThemeState();
    applyCSSVariables();
    
    // Watch for theme changes
    const observer = new MutationObserver(updateThemeState);
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['data-theme', 'class']
    });
  }

  return {
    isDarkMode: computed(() => isDarkMode.value),
    themeClasses: computed(() => themeClasses.value),
    componentVariants,
    telegramCSSVariables,
    safeAreaStyles: computed(() => safeAreaStyles.value),
    navigationAwareStyles: computed(() => navigationAwareStyles.value),
    responsive,
    applyCSSVariables,
    updateThemeState
  };
}