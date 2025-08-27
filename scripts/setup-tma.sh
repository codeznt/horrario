#!/usr/bin/env bash
set -euo pipefail

echo "[TMA] Telegram Mini Apps one-shot setup starting..."

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

pkg_mgr="npm"
if command -v pnpm >/dev/null 2>&1; then pkg_mgr="pnpm"; fi
if command -v yarn >/dev/null 2>&1; then pkg_mgr="yarn"; fi

install_deps() {
  echo "[TMA] Installing dependencies (@telegram-apps/sdk, @telegram-apps/bridge)" 
  case "$pkg_mgr" in
    npm)
      npm install @telegram-apps/sdk @telegram-apps/bridge || true ;;
    pnpm)
      pnpm add @telegram-apps/sdk @telegram-apps/bridge || true ;;
    yarn)
      yarn add @telegram-apps/sdk @telegram-apps/bridge || true ;;
  esac
}

ensure_dir() { mkdir -p "$1"; }

write_init_ts() {
  local f="resources/js/telegram/init.ts"
  ensure_dir "resources/js/telegram"
  if [[ -f "$f" ]]; then
    echo "[TMA] Skipping init.ts (already exists): $f"
    return
  fi
  cat > "$f" << 'EOF'
import { init as initSDK, backButton, miniApp, themeParams, viewport, initData } from '@telegram-apps/sdk';
import { setDebug } from '@telegram-apps/bridge';

export async function initTelegram(debug = import.meta.env.DEV): Promise<void> {
  try { setDebug(!!debug); } catch {}
  try {
    const w = window as any;
    if (!w.TelegramWebviewProxy || typeof w.TelegramWebviewProxy.postEvent !== 'function') {
      w.TelegramWebviewProxy = {
        postEvent(event: string, payload?: string) {
          try {
            const data = payload ? JSON.parse(payload) : undefined;
            window.dispatchEvent(new MessageEvent('message', { data: JSON.stringify({ eventType: event, eventData: data }), source: window.parent }));
          } catch {}
        },
      };
    }
  } catch {}

  initSDK();
  try { backButton.mount(); } catch {}
  try { miniApp.mount(); } catch {}
  try { themeParams.mount(); } catch {}
  try { initData.restore(); } catch {}

  viewport.mount().then(() => { try { viewport.bindCssVars.isAvailable() && viewport.bindCssVars(); } catch {} }).catch(() => {});
  try { miniApp.bindCssVars.isAvailable() && miniApp.bindCssVars(); } catch {}
  try { themeParams.bindCssVars.isAvailable() && themeParams.bindCssVars(); } catch {}
}
EOF
  echo "[TMA] Wrote $f"
}

write_mock_ts() {
  local f="resources/js/telegram/mockEnv.ts"
  ensure_dir "resources/js/telegram"
  if [[ -f "$f" ]]; then
    echo "[TMA] Skipping mockEnv.ts (already exists): $f"
    return
  fi
  cat > "$f" << 'EOF'
import { isTMA, mockTelegramEnv } from '@telegram-apps/bridge';

export const telegramEnvReady: Promise<void> = import.meta.env.DEV
  ? (async () => {
      try { if (await isTMA()) return; } catch {}
      const initDataRaw = new URLSearchParams([
        ['user', JSON.stringify({ id: 99281932, first_name: 'Andrew', last_name: 'Rogue', username: 'rogue', language_code: 'en', is_premium: true, allows_write_to_pm: true })],
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
EOF
  echo "[TMA] Wrote $f"
}

patch_app_ts() {
  local f="resources/js/app.ts"
  if [[ ! -f "$f" ]]; then
    echo "[TMA] WARNING: $f not found — skip patch"
    return
  fi
  if ! grep -q "telegramEnvReady" "$f"; then
    awk 'NR==1{print; print "import { telegramEnvReady } from \x27./telegram/mockEnv\x27;"; next}1' "$f" > "$f.tmp" && mv "$f.tmp" "$f"
  fi
  if ! grep -q "initTelegram" "$f"; then
    awk 'NR==1{print; print "import { initTelegram } from \x27./telegram/init\x27;"; next}1' "$f" > "$f.tmp" && mv "$f.tmp" "$f"
  fi
  if ! grep -q "await telegramEnvReady;" "$f"; then
    # Insert just before createInertiaApp call
    awk '{print} /createInertiaApp\(/ && !x {print "\nawait telegramEnvReady;\nawait initTelegram(import.meta.env.DEV);\n"; x=1}' "$f" > "$f.tmp" && mv "$f.tmp" "$f"
  fi
  echo "[TMA] Patched $f"
}

patch_app_css() {
  local f="resources/css/app.css"
  if [[ ! -f "$f" ]]; then
    echo "[TMA] WARNING: $f not found — skip token block"
    return
  fi
  if ! grep -q "--color-tg-bg" "$f"; then
    cat >> "$f" << 'EOF'

/* Telegram theme tokens */
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
EOF
    echo "[TMA] Extended $f with Telegram tokens"
  else
    echo "[TMA] Skipping token block (already present): $f"
  fi
}

install_deps
write_init_ts
write_mock_ts
patch_app_ts
patch_app_css

echo "[TMA] Setup complete. Start dev with: $pkg_mgr run dev (or composer run dev)"

