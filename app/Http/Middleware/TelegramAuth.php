<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TelegramAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TelegramAuth
{
    public function __construct(
        private TelegramAuthService $telegramAuthService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is already authenticated
        if (Auth::check()) {
            return $next($request);
        }

        // Try to get Telegram init data from various sources
        $initData = $this->extractTelegramInitData($request);

        if (! $initData) {
            // In local/dev environments, attempt to generate a valid mock init data
            if (app()->environment(['local', 'development']) || config('app.debug')) {
                $initData = $this->generateDevMockInitData();
                if ($initData) {
                    Log::info('TelegramAuth: Using generated DEV mock init data');
                }
            }

            if (! $initData) {
                Log::debug('TelegramAuth: No init data found');

                return $this->unauthorizedResponse($request);
            }
        }

        // Parse and validate Telegram data
        $telegramData = $this->telegramAuthService->parseAndValidateTelegramData($initData);

        if (! $telegramData) {
            Log::warning('TelegramAuth: Invalid Telegram hash verification failed', [
                'init_data' => substr($initData, 0, 100).'...',
            ]);

            return $this->unauthorizedResponse($request);
        }

        // Check auth date validity
        if (! $this->telegramAuthService->isAuthDateValid($telegramData, (int) config('telegram.auth_max_age', 86400))) {
            Log::warning('TelegramAuth: Auth date is too old');

            return $this->unauthorizedResponse($request);
        }

        // Extract user data
        $userData = $this->telegramAuthService->extractUserData($telegramData);

        if (! $userData) {
            Log::warning('TelegramAuth: No user data found in Telegram data');

            return $this->unauthorizedResponse($request);
        }

        try {
            // Find or create user
            $user = User::findOrCreateFromTelegram($userData);

            // Authenticate user
            Auth::login($user);

            Log::info('TelegramAuth: User authenticated successfully', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
                'username' => $user->username,
            ]);

            // Add telegram data to request for potential use in controllers
            $request->merge(['telegram_data' => $telegramData]);

        } catch (\Exception $e) {
            Log::error('TelegramAuth: Failed to authenticate user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->unauthorizedResponse($request);
        }

        return $next($request);
    }

    /**
     * Extract Telegram init data from request.
     */
    private function extractTelegramInitData(Request $request): ?string
    {
        // Try multiple sources for init data (Telegram launch params + Inertia)
        return $request->header('X-Telegram-Init-Data')
            ?? $request->query('tgWebAppData')
            ?? $request->input('_telegram_auth')
            ?? $request->input('_auth')
            ?? $request->input('initData');
    }

    /**
     * Return unauthorized response based on request type.
     */
    private function unauthorizedResponse(Request $request): Response
    {
        // For JSON requests (like tests), return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized. Valid Telegram authentication required.',
                'error' => 'telegram_auth_required',
            ], 401);
        }

        // For Inertia requests, render the unauthorized page with proper Telegram styling
        return \Inertia\Inertia::render('Telegram/Unauthorized', [
            'message' => 'Telegram authentication required',
            'error' => 'telegram_auth_required',
        ])->toResponse($request)->setStatusCode(401);
    }

    // Development-only bypass helpers removed to keep middleware secure and minimal.

    /**
     * Generate valid Telegram init data for local development using the configured bot token.
     * Returns a query-string formatted init data matching Telegram's verification scheme.
     */
    private function generateDevMockInitData(): ?string
    {
        try {
            $botToken = config('telegram.bot_token');
            if (empty($botToken)) {
                return null;
            }

            $authDate = (string) time();
            $user = [
                'id' => 99281932,
                'first_name' => 'Andrew',
                'last_name' => 'Rogue',
                'username' => 'rogue',
                'language_code' => 'en',
                'is_premium' => true,
                'allows_write_to_pm' => true,
            ];

            $userJson = json_encode($user, JSON_UNESCAPED_SLASHES);

            // Data used for hash generation (without hash)
            $dataForHash = [
                'auth_date' => $authDate,
                'chat_instance' => '8428209589180549439',
                'chat_type' => 'sender',
                'start_param' => 'debug',
                'user' => $userJson,
            ];

            // Create data check string (sorted by key)
            ksort($dataForHash);
            $pairs = [];
            foreach ($dataForHash as $key => $value) {
                $pairs[] = $key.'='.$value;
            }
            $dataCheckString = implode("\n", $pairs);

            // secret_key = HMAC_SHA256(bot_token, 'WebAppData') using raw binary output
            $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
            // hash = HMAC_SHA256(data_check_string, secret_key) (hex output)
            $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

            // Build full init data (include hash)
            $initData = [
                'user' => $userJson,
                'auth_date' => $authDate,
                'start_param' => 'debug',
                'chat_type' => 'sender',
                'chat_instance' => '8428209589180549439',
                'hash' => $hash,
            ];

            return http_build_query($initData);
        } catch (\Throwable $e) {
            Log::debug('TelegramAuth: Failed to generate DEV mock init data', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
