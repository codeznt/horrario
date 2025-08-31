/**
 * API Response and Request Types
 * Provides type safety for API interactions and data structures
 */

// ====================================
// API Response Wrappers
// ====================================

export interface ApiResponse<T = unknown> {
    data: T;
    message?: string;
    status: 'success' | 'error';
    meta?: ApiMeta;
}

export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status_code: number;
}

export interface ApiMeta {
    current_page?: number;
    from?: number;
    last_page?: number;
    per_page?: number;
    to?: number;
    total?: number;
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
    meta: Required<ApiMeta>;
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}

// ====================================
// Authentication API Types
// ====================================

export interface LoginRequest {
    email: string;
    password: string;
    remember?: boolean;
}

export interface RegisterRequest {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role?: 'business' | 'customer';
    terms?: boolean;
}

export interface AuthResponse {
    user: import('./index').User;
    token?: string;
    message: string;
}

// ====================================
// User Management API Types
// ====================================

export interface UpdateProfileRequest {
    name?: string;
    email?: string;
    avatar?: File | string;
    phone?: string;
    address?: string;
}

export interface ChangePasswordRequest {
    current_password: string;
    password: string;
    password_confirmation: string;
}

// ====================================
// Provider API Types
// ====================================

export interface CreateProviderRequest {
    name: string;
    description?: string;
    address?: string;
    phone?: string;
    email?: string;
    avatar?: File;
}

export interface UpdateProviderRequest extends Partial<CreateProviderRequest> {
    id: number;
}

export interface ProviderSearchParams {
    query?: string;
    category?: string;
    location?: string;
    min_rating?: number;
    sort_by?: 'name' | 'rating' | 'created_at';
    sort_order?: 'asc' | 'desc';
    page?: number;
    per_page?: number;
}

// ====================================
// Service API Types
// ====================================

export interface CreateServiceRequest {
    provider_id: number;
    name: string;
    description?: string;
    duration: number; // minutes
    price: number; // cents
    currency: string;
    category?: string;
    is_active?: boolean;
}

export interface UpdateServiceRequest extends Partial<Omit<CreateServiceRequest, 'provider_id'>> {
    id: number;
}

export interface ServiceSearchParams {
    query?: string;
    provider_id?: number;
    category?: string;
    min_price?: number;
    max_price?: number;
    duration?: number;
    is_active?: boolean;
    page?: number;
    per_page?: number;
}

// ====================================
// Booking API Types
// ====================================

export interface CreateBookingRequest {
    service_id: number;
    scheduled_at: string; // ISO date string
    notes?: string;
}

export interface UpdateBookingRequest {
    id: number;
    status?: import('./index').BookingStatus;
    scheduled_at?: string;
    notes?: string;
}

export interface BookingSearchParams {
    user_id?: number;
    provider_id?: number;
    service_id?: number;
    status?: import('./index').BookingStatus;
    from_date?: string;
    to_date?: string;
    sort_by?: 'scheduled_at' | 'created_at' | 'status';
    sort_order?: 'asc' | 'desc';
    page?: number;
    per_page?: number;
}

// ====================================
// File Upload Types
// ====================================

export interface FileUploadResponse {
    path: string;
    url: string;
    filename: string;
    size: number;
    mime_type: string;
}

export interface UploadProgress {
    loaded: number;
    total: number;
    percentage: number;
}

// ====================================
// Validation Types
// ====================================

export interface ValidationError {
    field: string;
    message: string;
    rule?: string;
}

export interface ValidationResponse {
    valid: boolean;
    errors: ValidationError[];
}

// ====================================
// Search and Filter Types
// ====================================

export interface SearchFilters {
    query?: string;
    category?: string;
    location?: string;
    price_range?: [number, number];
    rating_min?: number;
    availability?: string; // ISO date string
    tags?: string[];
}

export interface SortOptions {
    field: string;
    direction: 'asc' | 'desc';
}

export interface SearchRequest {
    filters: SearchFilters;
    sort?: SortOptions;
    pagination: {
        page: number;
        per_page: number;
    };
}

// ====================================
// WebSocket/Real-time Types
// ====================================

export interface WebSocketMessage<T = unknown> {
    type: string;
    data: T;
    timestamp: string;
    user_id?: number;
}

export interface BookingStatusUpdate {
    booking_id: number;
    status: import('./index').BookingStatus;
    message?: string;
    updated_by: number;
}

export interface NotificationMessage {
    id: string;
    title: string;
    body: string;
    type: 'info' | 'success' | 'warning' | 'error';
    actions?: Array<{
        label: string;
        action: string;
    }>;
}