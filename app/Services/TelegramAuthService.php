<?php

namespace App\Services;

use InvalidArgumentException;

class TelegramAuthService
{
    private string $botToken;

    public function __construct()
    {
        $botToken = config('telegram.bot_token');
        
        if (empty($botToken)) {
            throw new InvalidArgumentException(
                'Telegram bot token not configured. Please set TELEGRAM_BOT_TOKEN in your .env file. ' .
                'For development, you can use a mock token like: 123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ-0123456789'
            );
        }
        
        $this->botToken = $botToken;
    }

    /**
     * Verify Telegram WebApp data hash according to official documentation.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function verifyTelegramHash(array $data): bool
    {
        if (!isset($data['hash'])) {
            return false;
        }

        $hash = $data['hash'];
        unset($data['hash']);

        // Create data check string according to Telegram docs
        $dataCheckString = $this->createDataCheckString($data);

        // Create secret key using HMAC-SHA256 with "WebAppData" as key and bot token as message
        $secretKey = hash_hmac('sha256', $this->botToken, 'WebAppData', true);

        // Calculate hash using the secret key and data check string
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($calculatedHash, $hash);
    }

    /**
     * Parse and validate Telegram init data from query string format.
     *
     * @param string $initData
     * @return array<string, mixed>|null
     */
    public function parseAndValidateTelegramData(string $initData): ?array
    {
        // Parse query string format
        parse_str($initData, $data);

        if (!$this->verifyTelegramHash($data)) {
            return null;
        }

        // Parse user data if present
        if (isset($data['user'])) {
            $userData = json_decode($data['user'], true);
            if ($userData === null) {
                return null;
            }
            $data['user'] = $userData;
        }

        return $data;
    }

    /**
     * Extract user data from validated Telegram data.
     *
     * @param array<string, mixed> $telegramData
     * @return array<string, mixed>|null
     */
    public function extractUserData(array $telegramData): ?array
    {
        if (!isset($telegramData['user'])) {
            return null;
        }

        return $telegramData['user'];
    }

    /**
     * Create data check string according to Telegram WebApp documentation.
     * 
     * @param array<string, mixed> $data
     * @return string
     */
    private function createDataCheckString(array $data): string
    {
        // Sort data by key and create key=value pairs
        ksort($data);
        
        $pairs = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $pairs[] = $key . '=' . $value;
        }

        return implode("\n", $pairs);
    }

    /**
     * Check if auth date is within acceptable time window (24 hours by default).
     *
     * @param array<string, mixed> $telegramData
     * @param int $maxAge Maximum age in seconds (default: 86400 = 24 hours)
     * @return bool
     */
    public function isAuthDateValid(array $telegramData, int $maxAge = 86400): bool
    {
        if (!isset($telegramData['auth_date'])) {
            return false;
        }

        $authDate = (int) $telegramData['auth_date'];
        $currentTime = time();

        return ($currentTime - $authDate) <= $maxAge;
    }
}
