<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'language_code',
        'is_premium',
        'allows_write_to_pm',
        'role',
        'completed_onboarding',
        'telegram_auth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'telegram_id' => 'integer',
            'is_premium' => 'boolean',
            'allows_write_to_pm' => 'boolean',
            'completed_onboarding' => 'boolean',
            'telegram_auth_date' => 'datetime',
        ];
    }

    /**
     * Find or create a user from Telegram data.
     *
     * @param array<string, mixed> $telegramData
     * @return static
     */
    public static function findOrCreateFromTelegram(array $telegramData): static
    {
        $telegramId = $telegramData['id'];

        $user = static::where('telegram_id', $telegramId)->first();

        if ($user) {
            // Update existing user with latest Telegram data
            $user->update([
                'first_name' => $telegramData['first_name'] ?? null,
                'last_name' => $telegramData['last_name'] ?? null,
                'username' => $telegramData['username'] ?? null,
                'language_code' => $telegramData['language_code'] ?? null,
                'is_premium' => $telegramData['is_premium'] ?? false,
                'allows_write_to_pm' => $telegramData['allows_write_to_pm'] ?? false,
                'telegram_auth_date' => now(),
            ]);

            return $user;
        }

        // Create new user from Telegram data
        $fullName = trim(($telegramData['first_name'] ?? '') . ' ' . ($telegramData['last_name'] ?? ''));
        $email = $telegramData['username'] ? $telegramData['username'] . '@telegram.local' : 'user' . $telegramId . '@telegram.local';

        return static::create([
            'telegram_id' => $telegramId,
            'name' => $fullName ?: 'Telegram User',
            'email' => $email,
            'password' => '', // Empty password since we authenticate via Telegram
            'first_name' => $telegramData['first_name'] ?? null,
            'last_name' => $telegramData['last_name'] ?? null,
            'username' => $telegramData['username'] ?? null,
            'language_code' => $telegramData['language_code'] ?? null,
            'is_premium' => $telegramData['is_premium'] ?? false,
            'allows_write_to_pm' => $telegramData['allows_write_to_pm'] ?? false,
            'role' => 'user',
            'telegram_auth_date' => now(),
        ]);
    }
}
