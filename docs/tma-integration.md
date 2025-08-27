# Telegram Mini Apps Integration with Laravel 12, Inertia.js, Vue 3, and Tailwind CSS v4

This document outlines the integration of the Telegram Mini Apps SDK into a Laravel 12 application using Inertia.js, Vue 3, and Tailwind CSS v4. The integration enables the application to run within Telegram’s Mini Apps environment, bind Telegram theme CSS variables to Tailwind v4 tokens, and provide robust local mocks for development outside Telegram.

## Purpose

- Seamlessly integrate Telegram Mini Apps SDK with an existing Laravel 12, Inertia.js, and Vue 3 application.
- Map Telegram theme CSS variables to Tailwind v4 tokens for consistent styling.
- Provide local mocks for development outside the Telegram environment.
- Include drop-in TypeScript files and a minimal Tailwind v4 `app.css` mapping.

## What This Integration Provides

- **Drop-in TypeScript Files**: `init.ts` for Telegram SDK configuration and `mockEnv.ts` for local development mocks.
- **Tailwind v4 Mapping**: Minimal `app.css` with `@theme inline` mapping for Telegram CSS variables.
- **Inertia.js Integration**: Instructions for wiring up the Telegram SDK in the Inertia entry point.
- **Verified Approach**: Aligned with Telegram Mini Apps SDK and Tailwind v4 documentation.

## Prerequisites

- Laravel 12 with Inertia.js and Vue 3.
- Tailwind CSS v4 (CSS-first, `@theme` directive).
- Vite-based frontend build.

## Installation

Install the required packages:

```bash
npm install @telegram-apps/sdk @telegram-apps/sdk-vue
npm install --save-dev eruda  # Optional dev console
```

## File Layout

- `resources/js/telegram/init.ts`: Configures Telegram SDK, mounts components, and binds CSS variables.
- `resources/js/telegram/mockEnv.ts`: Development-only mock for Telegram environment.
- `resources/css/app.css`: Tailwind v4 with Telegram CSS variable mappings.
- `resources/js/app.ts`: Inertia.js entry point for Telegram SDK initialization.

## Drop-in Files

### File: `resources/js/telegram/init.ts`

```typescript
import { init as initSDK, backButton, miniApp, themeParams, viewport, initData } from '@telegram-apps/sdk';
import { setDebug } from '@telegram-apps/bridge';

/**
 * Configure Telegram SDK, mount components, and bind CSS variables.
 * Call this from your Inertia client entry before mounting Vue.
 */
export async function initTelegram(debug = import.meta.env.DEV): Promise<void> {
  // Enable SDK debug logs in dev
  try { setDebug(!!debug); } catch {}

  // Initialize bridge/events once
  initSDK();

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

  // Mount components
  backButton.mount();
  miniApp.mount();
  themeParams.mount();
  initData.restore();

  // Mount viewport and bind its CSS vars
  try {
    await viewport.mount();
    if (viewport.bindCssVars.isAvailable()) {
      viewport.bindCssVars();
    }
  } catch (e) {
    console.error('Viewport mount failed', e);
  }

  // Bind app + theme CSS vars for Tailwind mapping
  if (miniApp.bindCssVars.isAvailable()) {
    miniApp.bindCssVars();
  }
  if (themeParams.bindCssVars.isAvailable()) {
    themeParams.bindCssVars();
  }
}
```

### File: `resources/js/telegram/mockEnv.ts`

```typescript
import { isTMA, mockTelegramEnv, retrieveLaunchParams } from '@telegram-apps/bridge';

/**
 * Development-only Telegram environment mock.
 * Ensures local dev runs outside Telegram clients.
 */
if (import.meta.env.DEV) {
  (async () => {
    try {
      if (await isTMA()) {
        return; // already in Telegram context
      }
    } catch {
      // Continue to mock if detection throws
    }

    let lp: any;
    let launchParamsInput: any;

    // If launch params are present locally (e.g., via URL), use them
    try {
      lp = retrieveLaunchParams();
      launchParamsInput = lp;
    } catch {
      // Otherwise, craft a sane default
      const initDataRaw = new URLSearchParams([
        [
          'user',
          JSON.stringify({
            id: 1,
            first_name: 'Dev',
            username: 'local',
            language_code: 'en',
            is_premium: true,
            allows_write_to_pm: true,
          }),
        ],
        ['hash', 'debug-hash'],
        ['auth_date', String(Math.floor(Date.now() / 1000))],
        ['start_param', 'debug'],
        ['chat_type', 'sender'],
        ['chat_instance', '1'],
      ]).toString();

      const theme = {
        accent_text_color: '#6ab2f2',
        bg_color: '#17212b',
        button_color: '#5288c1',
        button_text_color: '#ffffff',
        destructive_text_color: '#ec3942',
        header_bg_color: '#17212b',
        hint_color: '#708499',
        link_color: '#6ab3f3',
        secondary_bg_color: '#232e3c',
        section_bg_color: '#17212b',
        section_header_text_color: '#6ab3f3',
        subtitle_text_color: '#708499',
        text_color: '#f5f5f5',
      } as const;

      const lpSearch = new URLSearchParams([
        ['tgWebAppData', initDataRaw],
        ['tgWebAppVersion', '8'],
        ['tgWebAppPlatform', 'web'],
        ['tgWebAppThemeParams', JSON.stringify(theme)],
      ]);

      launchParamsInput = lpSearch;
    }

    mockTelegramEnv({ launchParams: launchParamsInput });
    console.warn(
      'Telegram env mocked for DEV only. Do not enable in production.'
    );
  })();
}
```

### File: `resources/css/app.css`

```css
/* Tailwind v4 CSS-first setup */
@import "tailwindcss";

/* Map Telegram SDK CSS vars to Tailwind tokens.
   We use @theme inline to create Tailwind utilities like bg-tg-bg, text-tg-text, etc. */
@theme inline {
  /* Theme colors from Telegram */
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

  /* Optional: viewport dimension token if you want utilities based on Telegram viewport */
  --spacing-viewport-h: var(--tg-viewport-height, 100vh);
}

/* Example base styles using mapped tokens */
:root {
  color: var(--color-tg-text);
  background: var(--color-tg-secondary-bg);
}
```

## Inertia.js Integration

Wire up the Telegram SDK in your Inertia.js entry point:

### File: `resources/js/app.ts`

```typescript
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// 1) Import Tailwind CSS and Telegram mocks
import '../css/app.css';
import './telegram/mockEnv';

// 2) Initialize Telegram SDK (client-only)
import { initTelegram } from './telegram/init';
await initTelegram(import.meta.env.DEV);

// 3) Boot Inertia app
createInertiaApp({
  resolve: (name) => {
    return require(`./Pages/${name}.vue`);
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    app.use(plugin);
    app.mount(el);
  },
});
```

## Usage Examples in Vue Components

Use the Tailwind v4 tokens mapped to Telegram CSS variables in your Vue components:

- **Root Container**:
  ```html
  <div class="min-h-screen bg-tg-bg text-tg-text">...</div>
  ```
- **Buttons**:
  ```html
  <button class="bg-tg-accent text-tg-accent-foreground hover:opacity-90">Buy</button>
  ```
- **Subtle Text and Links**:
  ```html
  <p class="text-tg-hint">Hint text</p>
  <a class="text-tg-link underline">More</a>
  ```
- **Header Bar**:
  ```html
  <header class="bg-tg-header text-tg-section-header-text">Title</header>
  ```
- **Viewport-Aware Sizing**:
  ```html
  <main class="min-h-[--spacing-viewport-h]">...</main>
  ```

## Architecture Overview

- **SDK Mounting**: The `init.ts` file mounts `miniApp`, `backButton`, `themeParams`, and `viewport` once during client startup. The SDK listens for Telegram updates to keep the app in sync.
- **CSS Variables**: The `*.bindCssVars()` methods expose `--tg-theme-*` and `--tg-viewport-*` variables, which update dynamically with Telegram theme or viewport changes.
- **Tailwind v4 Mapping**: The `@theme inline` directive in `app.css` maps `--tg-theme-*` variables to Tailwind tokens (e.g., `--color-tg-bg`), enabling utilities like `bg-tg-bg` and `text-tg-text`.
- **Local Development Mocks**: The `mockEnv.ts` file fakes the Telegram environment during development, ensuring the app renders correctly outside Telegram.

## Best Practices

- **Development Mocks**: Use `mockEnv.ts` only in development, guarded by `import.meta.env.DEV`.
- **Support Checks**: Always use `*.isSupported()` and `*.bindCssVars.isAvailable()` to ensure compatibility.
- **Client-Side Initialization**: Call `initTelegram()` in the client-side Inertia entry point to avoid SSR issues.
- **Tailwind Mapping**: Use `@theme inline` to map Telegram CSS variables to Tailwind tokens for seamless styling.

## Production vs Development

- **Mocks**: Enabled only in development via `import.meta.env.DEV`. In production, the app gracefully no-ops if not in a Telegram context.
- **Error Handling**: The SDK checks ensure the app functions in non-Telegram environments, with mocks providing fallback styles in development.
- **Verification**:
    - **Outside Telegram (Dev)**:
        - Console displays “Telegram env mocked…” warning.
        - `:root` contains `--tg-theme-*` and `--color-tg-*` variables.
        - Tailwind utilities reflect mock colors.
    - **Inside Telegram**:
        - Colors sync with Telegram’s theme, updating dynamically.
        - `--tg-viewport-*` reflects the actual viewport, ensuring proper layout.

## References

- **Telegram Mini Apps SDK**: Documentation on binding CSS variables, availability checks, and lifecycle management (e.g., `themeParams.bindCssVars`, `miniApp.bindCssVars`, `viewport.bindCssVars`).
- **Tailwind CSS v4**: Documentation on `@theme` and `@theme inline` for mapping external CSS variables to Tailwind utilities.

## Repository Implementation Notes

We intentionally use the SDK core and Bridge directly:

- Components and lifecycle from `@telegram-apps/sdk`.
- Debug and environment utilities from `@telegram-apps/bridge` (`setDebug`, `mockTelegramEnv`, `retrieveLaunchParams`).

Rationale: `@telegram-apps/sdk-vue` wraps the SDK but doesn’t expose debug controls. Using the core SDK plus Bridge avoids re-export gaps and version drift, and keeps imports explicit.
