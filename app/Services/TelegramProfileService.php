<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramProfileService
{
    /**
     * Sync user profile with Telegram data.
     */
    public function syncProfile(User $user, array $telegramData): User
    {
        // Update basic profile information
        $firstName = $telegramData['first_name'] ?? '';
        $lastName = $telegramData['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        
        $user->update([
            'name' => $fullName ?: 'Telegram User',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $telegramData['username'] ?? null,
            'language_code' => $telegramData['language_code'] ?? null,
            'is_premium' => $telegramData['is_premium'] ?? false,
            'allows_write_to_pm' => $telegramData['allows_write_to_pm'] ?? false,
            'telegram_auth_date' => now(),
        ]);

        // Sync profile photo if available
        if (isset($telegramData['photo_url']) && !empty($telegramData['photo_url'])) {
            $this->syncProfilePhoto($user, $telegramData['photo_url']);
        }

        Log::info('Telegram profile synced for user', [
            'user_id' => $user->id,
            'telegram_id' => $user->telegram_id,
            'name' => $user->name,
        ]);

        return $user->fresh();
    }

    /**
     * Download and store Telegram profile photo.
     */
    protected function syncProfilePhoto(User $user, string $photoUrl): void
    {
        try {
            // Create HTTP context with user agent to avoid blocking
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (compatible; Laravel/11.0)',
                        'Accept: image/*',
                    ],
                    'timeout' => 30,
                ],
            ]);

            $photoContents = file_get_contents($photoUrl, false, $context);

            if ($photoContents === false) {
                Log::warning('Failed to download Telegram profile photo', [
                    'user_id' => $user->id,
                    'photo_url' => $photoUrl,
                ]);
                return;
            }

            // Validate image content
            $imageInfo = getimagesizefromstring($photoContents);
            if ($imageInfo === false) {
                Log::warning('Invalid image content from Telegram photo URL', [
                    'user_id' => $user->id,
                    'photo_url' => $photoUrl,
                ]);
                return;
            }

            // Generate unique filename
            $extension = $this->getImageExtension($imageInfo['mime']);
            $filename = 'telegram_' . $user->id . '_' . time() . '.' . $extension;
            $path = 'profile-photos/' . $filename;

            // Store the new photo
            $stored = Storage::disk('public')->put($path, $photoContents);

            if ($stored) {
                // Delete old photo if exists
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Update user with new photo path
                $user->update(['profile_photo_path' => $path]);

                Log::info('Telegram profile photo synced', [
                    'user_id' => $user->id,
                    'photo_path' => $path,
                    'file_size' => strlen($photoContents),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync Telegram profile photo', [
                'user_id' => $user->id,
                'photo_url' => $photoUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get file extension from MIME type.
     */
    protected function getImageExtension(string $mimeType): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    /**
     * Create or update user from Telegram data.
     */
    public function findOrCreateUser(array $telegramData): User
    {
        $telegramId = $telegramData['id'];

        // Try to find existing user
        $user = User::where('telegram_id', $telegramId)->first();

        if ($user) {
            // Update existing user
            return $this->syncProfile($user, $telegramData);
        }

        // Create new user
        $firstName = $telegramData['first_name'] ?? '';
        $lastName = $telegramData['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        $username = $telegramData['username'] ?? null;

        // Generate unique email
        $email = $username 
            ? $username . '@telegram.local'
            : 'user' . $telegramId . '@telegram.local';

        $user = User::create([
            'telegram_id' => $telegramId,
            'name' => $fullName ?: 'Telegram User',
            'email' => $email,
            'password' => '', // Empty password for Telegram auth
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'language_code' => $telegramData['language_code'] ?? null,
            'is_premium' => $telegramData['is_premium'] ?? false,
            'allows_write_to_pm' => $telegramData['allows_write_to_pm'] ?? false,
            'role' => 'user',
            'telegram_auth_date' => now(),
        ]);

        // Sync profile photo for new user
        if (isset($telegramData['photo_url']) && !empty($telegramData['photo_url'])) {
            $this->syncProfilePhoto($user, $telegramData['photo_url']);
        }

        Log::info('New user created from Telegram data', [
            'user_id' => $user->id,
            'telegram_id' => $telegramId,
            'name' => $user->name,
        ]);

        return $user;
    }

    /**
     * Get user's profile photo URL.
     */
    public function getProfilePhotoUrl(User $user): ?string
    {
        if (!$user->profile_photo_path) {
            return null;
        }

        return Storage::disk('public')->url($user->profile_photo_path);
    }

    /**
     * Clean up old profile photos for a user.
     */
    public function cleanupOldPhotos(User $user): void
    {
        try {
            $directory = 'profile-photos';
            $pattern = 'telegram_' . $user->id . '_';
            
            $files = Storage::disk('public')->files($directory);
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                // Skip current photo
                if ($user->profile_photo_path && $file === $user->profile_photo_path) {
                    continue;
                }
                
                // Delete old photos for this user
                if (Str::startsWith($filename, $pattern)) {
                    Storage::disk('public')->delete($file);
                    Log::info('Cleaned up old profile photo', [
                        'user_id' => $user->id,
                        'deleted_file' => $file,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old profile photos', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
