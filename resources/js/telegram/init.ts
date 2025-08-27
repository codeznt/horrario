import { init as initSDK, backButton, miniApp, themeParams, viewport, initData } from '@telegram-apps/sdk';
import { setDebug } from '@telegram-apps/bridge';

/**
 * Configure Telegram SDK, mount components, and bind CSS variables.
 * Call this from your Inertia client entry before mounting Vue.
 */
export async function initTelegram(debug = import.meta.env.DEV): Promise<void> {
    // Enable SDK debug logs in dev (bridge)
    try {
        setDebug(!!debug);
    } catch {
        /* noop if debug control not available */
    }

    // Ensure a postEvent target exists to avoid ERR_UNKNOWN_ENV outside iframe
    try {
        const w = window as any;
        if (!w.TelegramWebviewProxy || typeof w.TelegramWebviewProxy.postEvent !== 'function') {
            w.TelegramWebviewProxy = {
                postEvent(event: string, payload?: string) {
                    // Minimal shim: forward to window message bus for SDK mocks
                    try {
                        const data = payload ? JSON.parse(payload) : undefined;
                        window.dispatchEvent(
                            new MessageEvent('message', {
                                data: JSON.stringify({ eventType: event, eventData: data }),
                                source: window.parent,
                            }),
                        );
                    } catch {
                        // no-op
                    }
                },
            };
        }
    } catch {
        /* ignore shim errors */
    }

    // Initialize bridge/events once
    initSDK();

    // Always try to restore initData, even in mock environments
    try { 
        initData.restore(); 
    } catch {}

    // Ensure required components are supported
    if (!backButton.isSupported() || !miniApp.isSupported()) {
        // Not inside a Telegram context; mocks may kick in via mockEnv.ts
        return;
    }

    // Optionally load a dev console on device
    if (debug) {
        try {
            const eruda = await import('eruda');
            eruda.default.init();
        } catch (e) {
            console.warn('eruda init failed:', e);
        }
    }

    // Mount components (non-blocking where applicable)
    try { backButton.mount(); } catch {}
    try { miniApp.mount(); } catch {}
    try { themeParams.mount(); } catch {}

    // Mount viewport and bind its CSS vars without blocking app init
    viewport
        .mount()
        .then(() => {
            try {
                if (viewport.bindCssVars.isAvailable()) {
                    viewport.bindCssVars();
                }
            } catch {}
        })
        .catch(() => {});

    // Bind app + theme CSS vars for Tailwind mapping
    try {
        if (miniApp.bindCssVars.isAvailable()) {
            miniApp.bindCssVars();
        }
    } catch {}
    try {
        if (themeParams.bindCssVars.isAvailable()) {
            themeParams.bindCssVars();
        }
    } catch {}
}
