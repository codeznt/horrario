import { useTranslations } from '@/composables/useTranslations';

/**
 * Standardized status badge utilities with Telegram theme integration
 * Provides consistent status styling and translations across the application
 */
export function useStatusBadge() {
  const { t } = useTranslations();

  type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed' | 'declined';
  type ServiceStatus = 'active' | 'inactive' | 'draft';
  type PaymentStatus = 'paid' | 'pending' | 'failed' | 'refunded';

  /**
   * Get status variant for shadcn-vue Badge component
   * Uses Telegram theme colors for consistency
   */
  const getBookingStatusVariant = (status: BookingStatus) => {
    switch (status) {
      case 'confirmed':
        return 'default'; // Uses tg-accent
      case 'completed':
        return 'secondary';
      case 'pending':
        return 'outline';
      case 'cancelled':
      case 'declined':
        return 'destructive';
      default:
        return 'outline';
    }
  };

  /**
   * Get status styles for custom styling with Telegram theme vars
   */
  const getBookingStatusStyles = (status: BookingStatus) => {
    const baseStyles = 'px-2 py-1 text-xs rounded-full font-medium';
    
    switch (status) {
      case 'confirmed':
        return `${baseStyles} bg-tg-accent/20 text-tg-accent border border-tg-accent/30`;
      case 'completed':
        return `${baseStyles} bg-green-500/20 text-green-600 border border-green-500/30`;
      case 'pending':
        return `${baseStyles} bg-yellow-500/20 text-yellow-600 border border-yellow-500/30`;
      case 'cancelled':
      case 'declined':
        return `${baseStyles} bg-red-500/20 text-red-600 border border-red-500/30`;
      default:
        return `${baseStyles} bg-tg-section-bg text-tg-hint border border-tg-section-separator`;
    }
  };

  /**
   * Get translated status text
   */
  const getBookingStatusText = (status: BookingStatus): string => {
    return t(`app.booking_status_${status}`, {}, status.charAt(0).toUpperCase() + status.slice(1));
  };

  /**
   * Get service status variant
   */
  const getServiceStatusVariant = (status: ServiceStatus) => {
    switch (status) {
      case 'active':
        return 'default';
      case 'inactive':
        return 'secondary';
      case 'draft':
        return 'outline';
      default:
        return 'outline';
    }
  };

  /**
   * Get payment status styles with Telegram theme
   */
  const getPaymentStatusStyles = (status: PaymentStatus) => {
    const baseStyles = 'px-2 py-1 text-xs rounded-full font-medium';
    
    switch (status) {
      case 'paid':
        return `${baseStyles} bg-green-500/20 text-green-600 border border-green-500/30`;
      case 'pending':
        return `${baseStyles} bg-yellow-500/20 text-yellow-600 border border-yellow-500/30`;
      case 'failed':
        return `${baseStyles} bg-red-500/20 text-red-600 border border-red-500/30`;
      case 'refunded':
        return `${baseStyles} bg-blue-500/20 text-blue-600 border border-blue-500/30`;
      default:
        return `${baseStyles} bg-tg-section-bg text-tg-hint border border-tg-section-separator`;
    }
  };

  /**
   * Check if status indicates an active/positive state
   */
  const isActiveStatus = (status: string): boolean => {
    return ['confirmed', 'completed', 'active', 'paid'].includes(status);
  };

  /**
   * Check if status indicates a pending state
   */
  const isPendingStatus = (status: string): boolean => {
    return ['pending', 'draft'].includes(status);
  };

  /**
   * Check if status indicates an error/negative state
   */
  const isErrorStatus = (status: string): boolean => {
    return ['cancelled', 'declined', 'failed', 'inactive'].includes(status);
  };

  return {
    getBookingStatusVariant,
    getBookingStatusStyles,
    getBookingStatusText,
    getServiceStatusVariant,
    getPaymentStatusStyles,
    isActiveStatus,
    isPendingStatus,
    isErrorStatus
  };
}