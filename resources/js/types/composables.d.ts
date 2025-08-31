/**
 * TypeScript type definitions for Vue composables
 * Provides type safety and better IntelliSense for composable functions
 */

// ====================================
// Loading State Types
// ====================================

export interface LoadingState {
    isLoading: Readonly<Ref<boolean>>;
    error: Readonly<Ref<string | null>>;
    hasError: ComputedRef<boolean>;
    canProceed: ComputedRef<boolean>;
    executeWithLoading: <T>(operation: () => Promise<T>, errorMessage?: string) => Promise<T | null>;
    setLoading: (loading: boolean) => void;
    setError: (errorMessage: string | null) => void;
    clearError: () => void;
    reset: () => void;
}

export interface MultipleLoadingStates {
    setLoading: (key: string, loading: boolean) => void;
    setError: (key: string, error: string | null) => void;
    clearError: (key: string) => void;
    isLoading: (key: string) => ComputedRef<boolean>;
    hasError: (key: string) => ComputedRef<boolean>;
    getError: (key: string) => ComputedRef<string | null>;
    isAnyLoading: ComputedRef<boolean>;
    hasAnyError: ComputedRef<boolean>;
    executeWithLoading: <T>(key: string, operation: () => Promise<T>, errorMessage?: string) => Promise<T | null>;
}

// ====================================
// Telegram Theme Types
// ====================================

export interface TelegramThemeClasses {
    background: {
        primary: string;
        secondary: string;
        section: string;
        header: string;
        card: string;
    };
    text: {
        primary: string;
        secondary: string;
        hint: string;
        link: string;
        accent: string;
    };
    border: {
        separator: string;
        accent: string;
    };
    interactive: {
        accent: string;
        accentHover: string;
        button: string;
        buttonHover: string;
        destructive: string;
        success: string;
        warning: string;
    };
    layout: {
        padded: string;
        spacing: string;
        rounded: string;
        shadow: string;
    };
}

export interface TelegramThemeVariants {
    button: {
        primary: string;
        secondary: string;
        ghost: string;
        destructive: string;
    };
    card: {
        default: string;
        elevated: string;
        interactive: string;
    };
    input: {
        default: string;
        error: string;
    };
    badge: {
        default: string;
        secondary: string;
        success: string;
        warning: string;
        destructive: string;
    };
}

export interface TelegramThemeComposable {
    isDarkMode: ComputedRef<boolean>;
    themeClasses: ComputedRef<TelegramThemeClasses>;
    componentVariants: TelegramThemeVariants;
    telegramCSSVariables: Record<string, string>;
    safeAreaStyles: ComputedRef<Record<string, string>>;
    navigationAwareStyles: ComputedRef<Record<string, string>>;
    responsive: Record<string, string>;
    applyCSSVariables: () => void;
    updateThemeState: () => void;
}

// ====================================
// Date Formatting Types
// ====================================

export interface DateFormatOptions {
    locale?: string;
    timezone?: string;
    format?: 'short' | 'medium' | 'long' | 'full';
    includeTime?: boolean;
    relative?: boolean;
}

export interface DateFormattingComposable {
    formatDate: (dateString: string | Date, options?: DateFormatOptions) => string;
    formatTime: (dateString: string | Date, options?: Omit<DateFormatOptions, 'includeTime'>) => string;
    formatDateTime: (dateString: string | Date, options?: DateFormatOptions) => string;
    formatRelativeTime: (dateString: string | Date) => string;
    isToday: (dateString: string | Date) => boolean;
    isTomorrow: (dateString: string | Date) => boolean;
    isYesterday: (dateString: string | Date) => boolean;
    getTimeFromNow: (dateString: string | Date) => string;
}

// ====================================
// Status Badge Types
// ====================================

import type { BookingStatus, ServiceStatus, PaymentStatus } from './index';

export interface StatusBadgeComposable {
    getBookingStatusVariant: (status: BookingStatus) => string;
    getBookingStatusStyles: (status: BookingStatus) => string;
    getBookingStatusText: (status: BookingStatus) => string;
    getServiceStatusVariant: (status: ServiceStatus) => string;
    getPaymentStatusStyles: (status: PaymentStatus) => string;
    isActiveStatus: (status: string) => boolean;
    isPendingStatus: (status: string) => boolean;
    isErrorStatus: (status: string) => boolean;
}

// ====================================
// Translations Types
// ====================================

export interface TranslationComposable {
    t: (key: string, replacements?: Record<string, string | number>, defaultValue?: string) => string;
    getLocale: () => string;
    setLocale: (locale: string) => void;
    hasTranslation: (key: string) => boolean;
}

// ====================================
// Common Vue Types (for reference)
// ====================================

import type { Ref, ComputedRef } from 'vue';