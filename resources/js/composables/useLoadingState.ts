import { ref, computed } from 'vue';

/**
 * Shared loading state management with Telegram theme integration
 * Provides consistent loading states and error handling across the application
 */
export function useLoadingState() {
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  /**
   * Execute an async operation with loading state
   * @param operation - Async function to execute
   * @param errorMessage - Optional custom error message
   */
  const executeWithLoading = async <T>(
    operation: () => Promise<T>,
    errorMessage?: string
  ): Promise<T | null> => {
    isLoading.value = true;
    error.value = null;

    try {
      const result = await operation();
      return result;
    } catch (err) {
      error.value = errorMessage || (err instanceof Error ? err.message : 'An error occurred');
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Set loading state manually
   */
  const setLoading = (loading: boolean) => {
    isLoading.value = loading;
  };

  /**
   * Set error state manually
   */
  const setError = (errorMessage: string | null) => {
    error.value = errorMessage;
  };

  /**
   * Clear error state
   */
  const clearError = () => {
    error.value = null;
  };

  /**
   * Reset all states
   */
  const reset = () => {
    isLoading.value = false;
    error.value = null;
  };

  /**
   * Computed state helpers
   */
  const hasError = computed(() => !!error.value);
  const canProceed = computed(() => !isLoading.value && !hasError.value);

  return {
    isLoading: computed(() => isLoading.value),
    error: computed(() => error.value),
    hasError,
    canProceed,
    executeWithLoading,
    setLoading,
    setError,
    clearError,
    reset
  };
}

/**
 * Multiple loading states manager
 * Useful for managing multiple concurrent operations
 */
export function useMultipleLoadingStates() {
  const loadingStates = ref<Record<string, boolean>>({});
  const errors = ref<Record<string, string | null>>({});

  const setLoading = (key: string, loading: boolean) => {
    loadingStates.value[key] = loading;
  };

  const setError = (key: string, error: string | null) => {
    errors.value[key] = error;
  };

  const clearError = (key: string) => {
    errors.value[key] = null;
  };

  const isLoading = (key: string) => computed(() => loadingStates.value[key] || false);
  const hasError = (key: string) => computed(() => !!errors.value[key]);
  const getError = (key: string) => computed(() => errors.value[key]);

  const isAnyLoading = computed(() => Object.values(loadingStates.value).some(Boolean));
  const hasAnyError = computed(() => Object.values(errors.value).some(Boolean));

  const executeWithLoading = async <T>(
    key: string,
    operation: () => Promise<T>,
    errorMessage?: string
  ): Promise<T | null> => {
    setLoading(key, true);
    setError(key, null);

    try {
      const result = await operation();
      return result;
    } catch (err) {
      setError(key, errorMessage || (err instanceof Error ? err.message : 'An error occurred'));
      return null;
    } finally {
      setLoading(key, false);
    }
  };

  return {
    setLoading,
    setError,
    clearError,
    isLoading,
    hasError,
    getError,
    isAnyLoading,
    hasAnyError,
    executeWithLoading
  };
}