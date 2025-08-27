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
          // Generate valid hash for our mock bot token
          const authDate = Math.floor(Date.now() / 1000).toString();
          const userJson = JSON.stringify({
              id: 99281932,
              first_name: 'Andrew',
              last_name: 'Rogue',
              username: 'rogue',
              language_code: 'en',
              is_premium: true,
              allows_write_to_pm: true,
          });
          
          // Create data for hash generation (must match server-side algorithm)
          // Use a DEV-ONLY token; must match TELEGRAM_BOT_TOKEN on backend locally.
          const mockBotToken =
              import.meta.env.VITE_TELEGRAM_BOT_TOKEN ||
              '123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ-0123456789';
          const dataForHash = {
              auth_date: authDate,
              chat_instance: '8428209589180549439',
              chat_type: 'sender',
              start_param: 'debug',
              user: userJson,
          };
          
          // Generate hash using the same algorithm as server
          const dataCheckString = Object.keys(dataForHash)
              .sort()
              .map(key => `${key}=${dataForHash[key]}`)
              .join('\n');
              
          // Generate secret key and hash using WebCrypto (matches server algo)
          const generateMockHash = async (dataString: string, botToken: string): Promise<string> => {
              const encoder = new TextEncoder();
              const keyData = encoder.encode('WebAppData');
              const tokenData = encoder.encode(botToken);

              // secret_key = HMAC_SHA256(bot_token, "WebAppData")
              const tokenKey = await crypto.subtle.importKey('raw', tokenData, { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']);
              const secretKeyBuffer = await crypto.subtle.sign('HMAC', tokenKey, keyData);

              // hash = HMAC_SHA256(data_check_string, secret_key)
              const secretKey = await crypto.subtle.importKey('raw', secretKeyBuffer, { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']);
              const hashBuffer = await crypto.subtle.sign('HMAC', secretKey, encoder.encode(dataString));
              const hashArray = Array.from(new Uint8Array(hashBuffer));
              return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
          };
          
          const mockHash = await generateMockHash(dataCheckString, mockBotToken);

          const initDataRaw = new URLSearchParams([
              ['user', userJson],
              ['auth_date', authDate],
              ['start_param', 'debug'],
              ['chat_type', 'sender'],
              ['chat_instance', '8428209589180549439'],
              ['hash', mockHash],
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
