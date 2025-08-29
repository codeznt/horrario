import { initData } from '@telegram-apps/sdk';

export interface TelegramUser {
    id: number;
    first_name: string;
    last_name?: string;
    username?: string;
    language_code?: string;
    is_premium?: boolean;
    allows_write_to_pm?: boolean;
    photo_url?: string;
}

export interface TelegramAuthData {
    initData: string;
    user: TelegramUser | null;
}

export class TelegramAuth {
    private telegram: any;
    private initDataRaw: string | null = null;

    constructor() {
        this.telegram = (window as any).Telegram?.WebApp;
        this.initDataRaw = this.extractInitData();
    }

    /**
     * Check if Telegram WebApp is available.
     */
    isAvailable(): boolean {
        return !!this.telegram && !!this.initDataRaw;
    }

    /**
     * Extract init data from Telegram WebApp or SDK.
     */
    private extractInitData(): string | null {
        try {
            // Try to get from Telegram SDK first
            if (initData.raw) {
                return initData.raw() || null;
            }
        } catch {
            // Fall back to WebApp if SDK fails
        }

        // Try to get from Telegram WebApp
        if (this.telegram?.initData) {
            return this.telegram.initData;
        }

        // Check URL parameters as fallback
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('tgWebAppData') || null;
    }

    /**
     * Get user data from Telegram WebApp.
     */
    getUserData(): TelegramAuthData | null {
        if (!this.isAvailable()) {
            return null;
        }

        try {
            let user: TelegramUser | null = null;

            // Try to get user from SDK first
            try {
                if (initData.user) {
                    const sdkUser = initData.user();
                    if (sdkUser) {
                        user = {
                            id: sdkUser.id,
                            first_name: sdkUser.firstName,
                            last_name: sdkUser.lastName,
                            username: sdkUser.username,
                            language_code: sdkUser.languageCode,
                            is_premium: sdkUser.isPremium,
                            allows_write_to_pm: sdkUser.allowsWriteToPm,
                            photo_url: sdkUser.photoUrl,
                        };
                    }
                }
            } catch {
                // Fall back to WebApp user data
            }

            // Fall back to WebApp user if SDK fails
            if (!user && this.telegram?.initDataUnsafe?.user) {
                const webAppUser = this.telegram.initDataUnsafe.user;
                user = {
                    id: webAppUser.id,
                    first_name: webAppUser.first_name,
                    last_name: webAppUser.last_name,
                    username: webAppUser.username,
                    language_code: webAppUser.language_code,
                    is_premium: webAppUser.is_premium,
                    allows_write_to_pm: webAppUser.allows_write_to_pm,
                    photo_url: webAppUser.photo_url,
                };
            }

            return {
                initData: this.initDataRaw!,
                user,
            };
        } catch (error) {
            console.error('Failed to get Telegram user data:', error);
            return null;
        }
    }

    /**
     * Attach Telegram auth header to request headers.
     */
    attachAuthHeader(headers: Record<string, string> = {}): Record<string, string> {
        if (this.initDataRaw) {
            headers['X-Telegram-Init-Data'] = this.initDataRaw;
        }
        return headers;
    }

    /**
     * Setup Axios interceptor to automatically add Telegram auth headers.
     */
    setupAxiosInterceptor(): void {
        if (typeof window !== 'undefined' && (window as any).axios) {
            const axios = (window as any).axios;

            axios.interceptors.request.use((config: any) => {
                config.headers = this.attachAuthHeader(config.headers || {});
                return config;
            });
        }
    }

    /**
     * Setup Inertia request interceptor for Telegram auth.
     */
    setupInertiaInterceptor(): void {
        if (typeof window !== 'undefined' && (window as any).Inertia) {
            const { Inertia } = window as any;

            // Add auth header to all Inertia requests
            Inertia.on('before', (event: any) => {
                if (this.initDataRaw) {
                    event.detail.visit.headers = {
                        ...event.detail.visit.headers,
                        'X-Telegram-Init-Data': this.initDataRaw,
                    };
                }
            });
        }
    }

    /**
     * Get Telegram theme parameters.
     */
    getThemeParams(): Record<string, string> {
        if (!this.telegram) {
            return {};
        }

        return this.telegram.themeParams || {};
    }

    /**
     * Check if user is in dark mode.
     */
    isDarkMode(): boolean {
        return this.telegram?.colorScheme === 'dark';
    }

    /**
     * Expand the WebApp to full height.
     */
    expand(): void {
        if (this.telegram?.expand) {
            this.telegram.expand();
        }
    }

    /**
     * Show main button with text and callback.
     */
    showMainButton(text: string, callback: () => void): void {
        if (!this.telegram?.MainButton) return;

        this.telegram.MainButton.setText(text);
        this.telegram.MainButton.onClick(callback);
        this.telegram.MainButton.show();
    }

    /**
     * Hide main button.
     */
    hideMainButton(): void {
        if (this.telegram?.MainButton) {
            this.telegram.MainButton.hide();
        }
    }

    /**
     * Show back button with callback.
     */
    showBackButton(callback: () => void): void {
        if (!this.telegram?.BackButton) return;

        this.telegram.BackButton.onClick(callback);
        this.telegram.BackButton.show();
    }

    /**
     * Hide back button.
     */
    hideBackButton(): void {
        if (this.telegram?.BackButton) {
            this.telegram.BackButton.hide();
        }
    }

    /**
     * Show haptic feedback.
     */
    hapticFeedback(type: 'impact' | 'notification' | 'selection' = 'impact'): void {
        if (!this.telegram?.HapticFeedback) return;

        switch (type) {
            case 'impact':
                this.telegram.HapticFeedback.impactOccurred('medium');
                break;
            case 'notification':
                this.telegram.HapticFeedback.notificationOccurred('success');
                break;
            case 'selection':
                this.telegram.HapticFeedback.selectionChanged();
                break;
        }
    }

    /**
     * Close the WebApp.
     */
    close(): void {
        if (this.telegram?.close) {
            this.telegram.close();
        }
    }
}
