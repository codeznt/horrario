<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Plank\Metable\Metable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Metable, Notifiable;

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
        'telegram_auth_date',
        'profile_photo_path',
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
            'telegram_auth_date' => 'datetime',
        ];
    }

    /**
     * Check if the user has completed onboarding.
     */
    public function hasCompletedOnboarding(): bool
    {
        return (bool) $this->getMeta('completed_onboarding', false);
    }

    /**
     * Mark the user as having completed onboarding.
     */
    public function markOnboardingCompleted(): self
    {
        $this->setMeta('completed_onboarding', true);

        return $this;
    }

    /**
     * Reset the user's onboarding status.
     */
    public function resetOnboardingStatus(): self
    {
        $this->setMeta('completed_onboarding', false);

        return $this;
    }

    /**
     * Accessor for completed_onboarding to maintain backward compatibility.
     */
    public function getCompletedOnboardingAttribute(): bool
    {
        return $this->hasCompletedOnboarding();
    }

    /**
     * Mutator for completed_onboarding to maintain backward compatibility.
     */
    public function setCompletedOnboardingAttribute(bool $value): void
    {
        $this->setMeta('completed_onboarding', $value);
    }

    /**
     * Find or create a user from Telegram data.
     *
     * @param  array<string, mixed>  $telegramData
     */
    public static function findOrCreateFromTelegram(array $telegramData): static
    {
        $telegramProfileService = app(\App\Services\TelegramProfileService::class);

        return $telegramProfileService->findOrCreateUser($telegramData);
    }

    /**
     * Get the provider profile associated with the user.
     */
    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class);
    }

    /**
     * Get the bookings made by the user.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
