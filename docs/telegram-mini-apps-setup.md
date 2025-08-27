# Telegram Mini Apps – Clean Integration Guide (Laravel + Inertia + Vue 3 + Tailwind v4)

This guide describes the minimal, reliable setup to run your app inside Telegram Mini Apps (TMA) and locally with a development mock. Follow these steps from a fresh repo to working integration.

## 1) Prerequisites
- Laravel 12 with Inertia.js and Vue 3.
- Tailwind CSS v4 (CSS-first `@theme`).
- Vite build tooling.

## 2) Install Packages
```bash
npm install @telegram-apps/sdk @telegram-apps/bridge
# Optional in-dev console
npm install -D eruda
```

## 3) Telegram SDK Initialization
Create `resources/js/telegram/init.ts` and initialize the SDK before mounting Vue. Use non-blocking mounts so the app never stalls outside Telegram.

```ts
// resources/js/telegram/init.ts
import { init as initSDK, backButton, miniApp, themeParams, viewport, initData } from '@telegram-apps/sdk';
import { setDebug } from '@telegram-apps/bridge';

export async function initTelegram(debug = import.meta.env.DEV): Promise<void> {
  try { setDebug(!!debug); } catch {}

  // Provide a minimal postEvent shim when running outside Telegram
  try {
    const w = window as any;
    if (!w.TelegramWebviewProxy || typeof w.TelegramWebviewProxy.postEvent !== 'function') {
      w.TelegramWebviewProxy = {
        postEvent(event: string, payload?: string) {
          try {
            const data = payload ? JSON.parse(payload) : undefined;
            window.dispatchEvent(
              new MessageEvent('message', { data: JSON.stringify({ eventType: event, eventData: data }), source: window.parent })
            );
          } catch {}
        },
      };
    }
  } catch {}

  initSDK();

  // Mount components without blocking app startup
  try { backButton.mount(); } catch {}
  try { miniApp.mount(); } catch {}
  try { themeParams.mount(); } catch {}
  try { initData.restore(); } catch {}

  viewport
    .mount()
    .then(() => { try { viewport.bindCssVars.isAvailable() && viewport.bindCssVars(); } catch {} })
    .catch(() => {});

  try { miniApp.bindCssVars.isAvailable() && miniApp.bindCssVars(); } catch {}
  try { themeParams.bindCssVars.isAvailable() && themeParams.bindCssVars(); } catch {}
}
```

## 4) Development Mock (Local Only)
Create `resources/js/telegram/mockEnv.ts` to simulate Telegram on localhost. It uses `@telegram-apps/bridge` and stores a valid Launch Params query in local storage. Guard it by `import.meta.env.DEV`.

```ts
// resources/js/telegram/mockEnv.ts
import { isTMA, mockTelegramEnv } from '@telegram-apps/bridge';

export const telegramEnvReady: Promise<void> = import.meta.env.DEV
  ? (async () => {
      try { if (await isTMA()) return; } catch {}

      // tgWebAppData: raw init data (includes signature); theme: snake_case JSON
      const initDataRaw = new URLSearchParams([
        ['user', JSON.stringify({
          id: 99281932,
          first_name: 'Andrew', last_name: 'Rogue', username: 'rogue',
          language_code: 'en', is_premium: true, allows_write_to_pm: true,
        })],
        ['hash', '89d6079ad6762351f38c6dbbc41bb53048019256a9443988af7a48bcad16ba31'],
        ['signature', 'debug-signature'],
        ['auth_date', '1716922846'],
        ['start_param', 'debug'],
        ['chat_type', 'sender'],
        ['chat_instance', '8428209589180549439'],
      ]).toString();

      const theme = {
        accent_text_color: '#6ab2f2', bg_color: '#17212b', button_color: '#5288c1',
        button_text_color: '#ffffff', destructive_text_color: '#ec3942', header_bg_color: '#17212b',
        hint_color: '#708499', link_color: '#6ab3f3', secondary_bg_color: '#232e3c',
        section_bg_color: '#17212b', section_header_text_color: '#6ab3f3', subtitle_text_color: '#708499',
        text_color: '#f5f5f5',
      } as const;

      const lp = new URLSearchParams([
        ['tgWebAppData', initDataRaw],
        ['tgWebAppVersion', '8'],
        ['tgWebAppPlatform', 'tdesktop'],
        ['tgWebAppThemeParams', JSON.stringify(theme)],
      ]);

      mockTelegramEnv({ launchParams: lp });
      console.warn('Telegram env mocked for DEV only. Do not enable in production.');
    })()
  : Promise.resolve();
```

## 5) Wire Up Inertia Entry
Initialize the mock (dev-only) and the SDK before creating the Inertia app.

```ts
// resources/js/app.ts
import '../css/app.css';
import { telegramEnvReady } from './telegram/mockEnv';
import { initTelegram } from './telegram/init';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, type DefineComponent } from 'vue';

await telegramEnvReady;
await initTelegram(import.meta.env.DEV);

createInertiaApp({
  title: (title) => (title ? `${title} - ${import.meta.env.VITE_APP_NAME || 'Laravel'}` : (import.meta.env.VITE_APP_NAME || 'Laravel')),
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) }).use(plugin).mount(el);
  },
  progress: { color: '#4B5563' },
});
```

## 6) Tailwind Mapping for Telegram Tokens
Expose Telegram theme variables as Tailwind tokens. Extend your existing `resources/css/app.css` with a dedicated `@theme inline` block.

```css
/* resources/css/app.css */
@theme inline {
  --color-tg-bg: var(--tg-theme-bg-color, #ffffff);
  --color-tg-text: var(--tg-theme-text-color, #111827);
  --color-tg-hint: var(--tg-theme-hint-color, #6b7280);
  --color-tg-link: var(--tg-theme-link-color, #3b82f6);
  --color-tg-accent: var(--tg-theme-button-color, #3b82f6);
  --color-tg-accent-foreground: var(--tg-theme-button-text-color, #ffffff);
  --color-tg-destructive: var(--tg-theme-destructive-text-color, #ef4444);
  --color-tg-header: var(--tg-theme-header-bg-color, #111827);
  --color-tg-secondary-bg: var(--tg-theme-secondary-bg-color, #f3f4f6);
  --color-tg-section-bg: var(--tg-theme-section-bg-color, #f9fafb);
  --color-tg-section-separator: var(--tg-theme-section-separator-color, #e5e7eb);
  --color-tg-section-header-text: var(--tg-theme-section-header-text-color, #111827);
  --color-tg-subtitle-text: var(--tg-theme-subtitle-text-color, #6b7280);
  --spacing-viewport-h: var(--tg-viewport-height, 100vh);
}
```

## 7) Optional Demo Usage
Use tokens in a page to validate styling quickly:
- Root: `class="min-h-screen bg-tg-bg text-tg-text"`
- Button: `class="bg-tg-accent text-tg-accent-foreground"`
- Header: `class="bg-tg-header text-tg-section-header-text"`
- Viewport-aware: `class="min-h-[--spacing-viewport-h]"`

## 8) Run
- Local dev: `npm run dev` (or `composer run dev` to start PHP + Vite concurrently).
- Production: build assets `npm run build` and deploy normally. Mocks are dev-only and tree-shaken.

## 9) Notes & Recommendations
- Keep `mockEnv.ts` dev-only and avoid importing it in SSR or server code.
- Do not await long-running SDK methods in your app entry; mount non-blocking and bind CSS vars when available.
- Continue using your existing layout system (e.g., AppLayout) so pages render consistently.
- Prefer `@telegram-apps/sdk` + `@telegram-apps/bridge` for explicit, stable imports.
