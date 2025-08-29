<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;

class TmaInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tma:install {--no-install : Skip installing npm packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Telegram Mini Apps integration (SDK, mock, tokens)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fs = new Filesystem;
        $base = base_path();

        $writeIfMissing = function (string $path, string $contents) use ($fs, $base) {
            $full = $base.DIRECTORY_SEPARATOR.$path;
            if ($fs->exists($full)) {
                $this->info("[TMA] Skipping (exists): {$path}");

                return;
            }
            $fs->ensureDirectoryExists(dirname($full));
            $fs->put($full, $contents);
            $this->info("[TMA] Wrote: {$path}");
        };

        // 1) Write init.ts
        $writeIfMissing('resources/js/telegram/init.ts', <<<'TS'
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
            TS);

        // 2) Write mockEnv.ts
        $writeIfMissing('resources/js/telegram/mockEnv.ts', <<<'TS'
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
            TS);

        // 3) Patch resources/js/app.ts
        $appTs = $base.'/resources/js/app.ts';
        if ($fs->exists($appTs)) {
            $src = $fs->get($appTs);
            if (! str_contains($src, './telegram/mockEnv')) {
                $src = preg_replace('/^(import\\s+[^;]+;\n)/', '$0'."import { telegramEnvReady } from './telegram/mockEnv';\n", $src, 1);
            }
            if (! str_contains($src, './telegram/init')) {
                $src = preg_replace('/^(import\\s+[^;]+;\n)/', '$0'."import { initTelegram } from './telegram/init';\n", $src, 1);
            }
            if (! str_contains($src, 'await telegramEnvReady;')) {
                // Inject awaits before createInertiaApp call
                $src = preg_replace('/(createInertiaApp\s*\()/m', "\nawait telegramEnvReady;\nawait initTelegram(import.meta.env.DEV);\n\ncreateInertiaApp(", $src, 1);
            }
            $fs->put($appTs, $src);
            $this->info('[TMA] Patched resources/js/app.ts');
        } else {
            $this->warn('[TMA] resources/js/app.ts not found; skipping patch');
        }

        // 4) Patch resources/css/app.css
        $appCss = $base.'/resources/css/app.css';
        if ($fs->exists($appCss)) {
            $css = $fs->get($appCss);
            if (! str_contains($css, '--color-tg-bg')) {
                $css .= <<<'CSS'

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
                CSS;
                $fs->put($appCss, $css);
                $this->info('[TMA] Extended resources/css/app.css with Telegram tokens');
            } else {
                $this->info('[TMA] Token block already present in resources/css/app.css');
            }
        } else {
            $this->warn('[TMA] resources/css/app.css not found; skipping tokens');
        }

        // 5) Install NPM deps (optional)
        if (! $this->option('no-install')) {
            $this->info('[TMA] Installing npm packages…');
            try {
                // Prefer npm, fallback if missing.
                Process::run('npm install @telegram-apps/sdk @telegram-apps/bridge');
            } catch (\Throwable $e) {
                $this->warn('[TMA] Could not run npm install (skipped).');
            }
        } else {
            $this->info('[TMA] Skipped npm install per --no-install');
        }

        $this->info('[TMA] Done. Start dev with: npm run dev (or composer run dev)');
    }
}
