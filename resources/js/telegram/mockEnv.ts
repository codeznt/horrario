import { isTMA, mockTelegramEnv } from '@telegram-apps/bridge';
import type { LaunchParams } from '@telegram-apps/sdk';

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

          // Use the same mock data structure as the example file
          const initDataRaw = new URLSearchParams([
              ['user', JSON.stringify({
                  id: 99281932,
                  first_name: 'Andrew',
                  last_name: 'Rogue',
                  username: 'rogue',
                  language_code: 'en',
                  is_premium: true,
                  allows_write_to_pm: true,
              })],
              ['hash', '89d6079ad6762351f38c6dbbc41bb53048019256a9443988af7a48bcad16ba31'],
              ['auth_date', '1716922846'],
              ['start_param', 'debug'],
              ['chat_type', 'sender'],
              ['chat_instance', '8428209589180549439'],
          ]).toString();


          // Create initData object directly instead of parsing
          const initDataObject = {
              user: {
                  id: 99281932,
                  firstName: 'Andrew',
                  lastName: 'Rogue',
                  username: 'rogue',
                  languageCode: 'en',
                  isPremium: true,
                  allowsWriteToPm: true,
              },
              hash: '89d6079ad6762351f38c6dbbc41bb53048019256a9443988af7a48bcad16ba31',
              authDate: new Date(1716922846 * 1000),
              startParam: 'debug',
              chatType: 'sender',
              chatInstance: '8428209589180549439',
          };

          const launchParams: LaunchParams = {
              initData: initDataObject,
              initDataRaw,
              version: '8',
              platform: 'tdesktop',
              themeParams: {
                  accentTextColor: '#6ab2f2',
                  bgColor: '#17212b',
                  buttonColor: '#5288c1',
                  buttonTextColor: '#ffffff',
                  destructiveTextColor: '#ec3942',
                  headerBgColor: '#17212b',
                  hintColor: '#708499',
                  linkColor: '#6ab3f3',
                  secondaryBgColor: '#232e3c',
                  sectionBgColor: '#17212b',
                  sectionHeaderTextColor: '#6ab3f3',
                  subtitleTextColor: '#708499',
                  textColor: '#f5f5f5',
              },
          };

          mockTelegramEnv(launchParams);
          console.warn('Telegram env mocked for DEV only. Do not enable in production.');
      })()
    : Promise.resolve();
