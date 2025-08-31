import { InertiaLinkProps } from '@inertiajs/vue3';

// ====================================
// Authentication & User Types
// ====================================

export interface Auth {
    user: User;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role?: UserRole;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type UserRole = 'business' | 'customer' | 'user';

// ====================================
// Navigation & UI Types
// ====================================

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: string;
    isActive?: boolean;
}

// ====================================
// App & Page Types
// ====================================

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
}

// ====================================
// Business Domain Types
// ====================================

export interface Provider {
    id: number;
    name: string;
    description?: string;
    address?: string;
    phone?: string;
    email?: string;
    avatar?: string;
    rating?: number;
    reviews_count?: number;
    services?: Service[];
    created_at: string;
    updated_at: string;
}

export interface Service {
    id: number;
    provider_id: number;
    name: string;
    description?: string;
    duration: number; // in minutes
    price: number; // in cents
    currency: string;
    is_active: boolean;
    category?: string;
    provider?: Provider;
    created_at: string;
    updated_at: string;
}

export interface Booking {
    id: number;
    user_id: number;
    service_id: number;
    provider_id: number;
    status: BookingStatus;
    scheduled_at: string;
    notes?: string;
    total_price: number;
    currency: string;
    user?: User;
    service?: Service;
    provider?: Provider;
    created_at: string;
    updated_at: string;
}

// ====================================
// Status Types
// ====================================

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed' | 'declined';
export type ServiceStatus = 'active' | 'inactive' | 'draft';
export type PaymentStatus = 'paid' | 'pending' | 'failed' | 'refunded';

// ====================================
// Form & Component Types
// ====================================

export interface FormError {
    message: string;
    field?: string;
}

export interface FormData<T = Record<string, unknown>> {
    data: T;
    errors: Record<string, string>;
    processing: boolean;
    wasSuccessful: boolean;
    recentlySuccessful: boolean;
}

// Telegram Mini Apps theme types
export interface TelegramTheme {
    bg_color: string;
    text_color: string;
    hint_color: string;
    link_color: string;
    button_color: string;
    button_text_color: string;
    secondary_bg_color: string;
    header_bg_color: string;
    accent_text_color: string;
    section_bg_color: string;
    section_header_text_color: string;
    subtitle_text_color: string;
    destructive_text_color: string;
    section_separator_color: string;
}

export interface TelegramUser {
    id: number;
    first_name: string;
    last_name?: string;
    username?: string;
    language_code?: string;
    is_premium?: boolean;
    photo_url?: string;
}

// ====================================
// Legacy Type Aliases (for backward compatibility)
// ====================================

export type BreadcrumbItemType = BreadcrumbItem;
