import { router } from '@inertiajs/vue3'
import { initData } from '@telegram-apps/sdk'

/**
 * Composable for handling Telegram authentication with Inertia.js
 */
export function useTelegramAuth() {
    /**
     * Setup Telegram authentication by configuring Inertia to automatically
     * include Telegram init data with all requests
     */
    const setupTelegramAuth = () => {
        // Add a before hook to include Telegram init data in all requests
        router.on('before', (event) => {
            try {
                // Get raw init data from Telegram SDK
                const rawInitData = initData.raw()
                
                if (rawInitData) {
                    // Add init data to all requests via header
                    if (!event.detail.visit.headers) {
                        event.detail.visit.headers = {}
                    }
                    
                    event.detail.visit.headers['X-Telegram-Init-Data'] = rawInitData
                    
                    // Also add as form data for POST requests if not already present
                    if (event.detail.visit.method !== 'GET' && event.detail.visit.data) {
                        if (typeof event.detail.visit.data === 'object' && !event.detail.visit.data._telegram_auth) {
                            event.detail.visit.data._telegram_auth = rawInitData
                        }
                    }
                }
            } catch (error) {
                // In development or if SDK not available, this is expected
                console.debug('Telegram init data not available:', error)
            }
        })
    }

    /**
     * Get current Telegram init data
     */
    const getTelegramInitData = () => {
        try {
            return initData.raw()
        } catch (error) {
            console.debug('Telegram init data not available:', error)
            return null
        }
    }

    /**
     * Check if we're running in Telegram context
     */
    const isTelegramContext = () => {
        try {
            return !!initData.raw()
        } catch (error) {
            return false
        }
    }

    /**
     * Get parsed user data from Telegram
     */
    const getTelegramUser = () => {
        try {
            const data = initData.parsed()
            return data?.user || null
        } catch (error) {
            console.debug('Telegram user data not available:', error)
            return null
        }
    }

    return {
        setupTelegramAuth,
        getTelegramInitData,
        isTelegramContext,
        getTelegramUser,
    }
}