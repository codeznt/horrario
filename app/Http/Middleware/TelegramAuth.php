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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is already authenticated
        if (Auth::check()) {
            return $next($request);
        }

        // Try to get Telegram init data from various sources
        $initData = $this->extractTelegramInitData($request);
        
        if (!$initData) {
            Log::debug('TelegramAuth: No init data found');
            
            // Development bypass: if no init data and in local environment, 
            // create mock user directly for testing
            if (app()->environment('local')) {
                Log::info('TelegramAuth: Using development bypass for local environment');
                $mockUserData = [
                    'id' => 99281932,
                    'first_name' => 'Andrew',
                    'last_name' => 'Rogue', 
                    'username' => 'rogue',
                    'language_code' => 'en',
                    'is_premium' => true,
                    'allows_write_to_pm' => true,
                ];
                
                try {
                    $user = User::findOrCreateFromTelegram($mockUserData);
                    Auth::login($user);
                    
                    Log::info('TelegramAuth: Development bypass successful', [
                        'user_id' => $user->id,
                        'telegram_id' => $user->telegram_id,
                    ]);
                    
                    return $next($request);
                } catch (\Exception $e) {
                    Log::error('TelegramAuth: Development bypass failed', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            return $this->unauthorizedResponse($request);
        }

        // Parse and validate Telegram data
        $telegramData = $this->telegramAuthService->parseAndValidateTelegramData($initData);
        
        if (!$telegramData) {
            // Development fallback: if hash verification fails but we're in local development
            // and the init data looks like our mock data, allow it through
            if (app()->environment('local') && $this->isValidMockData($initData)) {
                Log::info('TelegramAuth: Using development mock data bypass');
                $telegramData = $this->parseDevMockData($initData);
            } else {
                Log::warning('TelegramAuth: Invalid Telegram hash verification failed', [
                    'init_data' => substr($initData, 0, 100) . '...'
                ]);
                return $this->unauthorizedResponse($request);
            }
        }

        // Check auth date validity
        if (!$this->telegramAuthService->isAuthDateValid($telegramData)) {
            Log::warning('TelegramAuth: Auth date is too old');
            return $this->unauthorizedResponse($request);
        }

        // Extract user data
        $userData = $this->telegramAuthService->extractUserData($telegramData);
        
        if (!$userData) {
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
        // Try multiple sources for init data (Inertia-compatible)
        $initData = $request->header('X-Telegram-Init-Data')
            ?? $request->input('_telegram_auth')
            ?? $request->input('_auth')
            ?? $request->input('initData');
        
        
        return $initData;
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
                'error' => 'telegram_auth_required'
            ], 401);
        }

        // For Inertia requests, render the unauthorized page with proper Telegram styling
        return \Inertia\Inertia::render('Telegram/Unauthorized', [
            'message' => 'Telegram authentication required',
            'error' => 'telegram_auth_required'
        ])->toResponse($request)->setStatusCode(401);
    }

    /**
     * Check if the init data looks like our development mock data.
     */
    private function isValidMockData(string $initData): bool
    {
        // Parse the data
        parse_str($initData, $data);
        
        // Check if it has the mock user ID and required fields
        if (!isset($data['user'])) {
            return false;
        }
        
        $userData = json_decode($data['user'], true);
        if (!$userData) {
            return false;
        }
        
        // Check if it matches our mock user (ID: 99281932, username: rogue)
        return isset($userData['id']) && 
               $userData['id'] === 99281932 && 
               isset($userData['username']) && 
               $userData['username'] === 'rogue';
    }

    /**
     * Parse development mock data without hash verification.
     */
    private function parseDevMockData(string $initData): array
    {
        parse_str($initData, $data);
        
        // Parse user data if present
        if (isset($data['user'])) {
            $userData = json_decode($data['user'], true);
            if ($userData !== null) {
                $data['user'] = $userData;
            }
        }
        
        // Ensure auth_date is recent for development
        if (!isset($data['auth_date'])) {
            $data['auth_date'] = (string) time();
        }
        
        return $data;
    }
}
