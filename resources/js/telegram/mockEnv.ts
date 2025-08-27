import { isTMA, mockTelegramEnv } from '@telegram-apps/bridge';

/**
 * Development-only Telegram environment mock.
 * Ensures local dev runs outside Telegram clients.
 * Exported promise lets callers await readiness before SDK init.
 */
export const telegramEnvReady: Promise<void> = import.meta.env.DEV
    ? (async () => {
          try {
              if (await isTMA()) {
                  return; // already in Telegram context
              }
          } catch {
              // Continue to mock if detection throws
          }

          // Align with docs/mock-example.ts (user data and colors)
          const initDataRaw = new URLSearchParams([
              [
                  'user',
                  JSON.stringify({
                      id: 99281932,
                      first_name: 'Andrew',
                      last_name: 'Rogue',
                      username: 'rogue',
                      language_code: 'en',
                      is_premium: true,
                      allows_write_to_pm: true,
                  }),
              ],
              ['hash', '89d6079ad6762351f38c6dbbc41bb53048019256a9443988af7a48bcad16ba31'],
              ['signature', 'debug-signature'],
              ['auth_date', '1716922846'],
              ['start_param', 'debug'],
              ['chat_type', 'sender'],
              ['chat_instance', '8428209589180549439'],
          ]).toString();

          // Use snake_case JSON for tgWebAppThemeParams when mocking via query
          const themeSnake = {
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
              ['tgWebAppPlatform', 'tdesktop'],
              ['tgWebAppThemeParams', JSON.stringify(themeSnake)],
          ]);

          mockTelegramEnv({ launchParams: lpSearch });
          console.warn('Telegram env mocked for DEV only. Do not enable in production.');
      })()
    : Promise.resolve();
