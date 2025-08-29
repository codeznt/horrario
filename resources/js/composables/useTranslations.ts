import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface PageProps {
    translations?: Record<string, any>;
    locale?: string;
    fallbackLocale?: string;
    [key: string]: any;
}

export function useTranslations() {
    const page = usePage<PageProps>();

    const translations = computed(() => page.props.translations || {});
    const locale = computed(() => page.props.locale || 'en');
    const fallbackLocale = computed(() => page.props.fallbackLocale || 'en');

    /**
     * Translate a key with optional parameters
     * @param key - Translation key (e.g., 'app.dashboard' or 'validation.required')
     * @param parameters - Optional parameters for string interpolation
     * @param fallback - Optional fallback text if key not found
     */
  const t = (key: string, parameters: Record<string, string | number> = {}, fallback?: string): string => {
    const resolve = (path: string[]): any => {
      let val: any = translations.value;
      for (const seg of path) {
        if (val && typeof val === 'object' && seg in val) {
          val = val[seg];
        } else {
          return undefined;
        }
      }
      return val;
    };

    const path = key.split('.');
    let value: any = resolve(path);

    // Fallback to the 'app' group when key not found at root
    if (value === undefined) {
      value = resolve(['app', ...path]);
    }

    // Use fallback or the key itself if translation not found
    if (value === undefined || value === null) {
      return fallback || key;
    }

    // If value is not a string, return it as-is
    if (typeof value !== 'string') {
      return String(value);
    }

    // Replace parameters in the translated string
    let result = value;
    Object.entries(parameters).forEach(([param, val]) => {
      result = result.replace(new RegExp(`:${param}\\b`, 'g'), String(val));
    });

    return result;
  };

    /**
     * Check if a translation key exists
     */
  const has = (key: string): boolean => {
    const resolve = (path: string[]): any => {
      let val: any = translations.value;
      for (const seg of path) {
        if (val && typeof val === 'object' && seg in val) {
          val = val[seg];
        } else {
          return undefined;
        }
      }
      return val;
    };

    const path = key.split('.');
    const direct = resolve(path);
    if (direct !== undefined && direct !== null) return true;
    const fallbackGroup = resolve(['app', ...path]);
    return fallbackGroup !== undefined && fallbackGroup !== null;
  };

    /**
     * Get current locale
     */
    const getLocale = (): string => locale.value;

    /**
     * Get fallback locale
     */
    const getFallbackLocale = (): string => fallbackLocale.value;

    return {
        t,
        has,
        getLocale,
        getFallbackLocale,
        translations: translations.value,
        locale: locale.value,
        fallbackLocale: fallbackLocale.value,
    };
}
