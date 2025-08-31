import { useTranslations } from '@/composables/useTranslations';

/**
 * Shared date formatting utilities with Telegram theme integration
 * Provides consistent date/time formatting across the application
 */
export function useDateFormatting() {
  const { getLocale } = useTranslations();
  
  /**
   * Format date in a user-friendly way
   * @param dateString - ISO date string or Date object
   * @param options - Intl.DateTimeFormat options
   */
  const formatDate = (dateString: string | Date, options?: Intl.DateTimeFormatOptions): string => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    
    const defaultOptions: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      ...options
    };
    
    return new Intl.DateTimeFormat(getLocale(), defaultOptions).format(date);
  };

  /**
   * Format time in user's locale
   * @param dateString - ISO date string or Date object
   * @param options - Intl.DateTimeFormat options
   */
  const formatTime = (dateString: string | Date, options?: Intl.DateTimeFormatOptions): string => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    
    const defaultOptions: Intl.DateTimeFormatOptions = {
      hour: '2-digit',
      minute: '2-digit',
      ...options
    };
    
    return new Intl.DateTimeFormat(getLocale(), defaultOptions).format(date);
  };

  /**
   * Format date and time together
   * @param dateString - ISO date string or Date object
   */
  const formatDateTime = (dateString: string | Date): string => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    
    return new Intl.DateTimeFormat(getLocale(), {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  };

  /**
   * Get relative time (e.g., "2 hours ago", "in 3 days")
   * @param dateString - ISO date string or Date object
   */
  const formatRelative = (dateString: string | Date): string => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    const now = new Date();
    const diffInSeconds = Math.floor((date.getTime() - now.getTime()) / 1000);
    
    const rtf = new Intl.RelativeTimeFormat(getLocale(), { numeric: 'auto' });
    
    // Convert to appropriate unit
    if (Math.abs(diffInSeconds) < 60) {
      return rtf.format(diffInSeconds, 'second');
    } else if (Math.abs(diffInSeconds) < 3600) {
      return rtf.format(Math.floor(diffInSeconds / 60), 'minute');
    } else if (Math.abs(diffInSeconds) < 86400) {
      return rtf.format(Math.floor(diffInSeconds / 3600), 'hour');
    } else {
      return rtf.format(Math.floor(diffInSeconds / 86400), 'day');
    }
  };

  /**
   * Check if date is today
   * @param dateString - ISO date string or Date object
   */
  const isToday = (dateString: string | Date): boolean => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    const today = new Date();
    
    return date.toDateString() === today.toDateString();
  };

  /**
   * Check if date is in the past
   * @param dateString - ISO date string or Date object
   */
  const isPast = (dateString: string | Date): boolean => {
    const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
    return date < new Date();
  };

  return {
    formatDate,
    formatTime,
    formatDateTime,
    formatRelative,
    isToday,
    isPast
  };
}