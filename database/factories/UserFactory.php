<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'telegram_id' => null,
            'first_name' => null,
            'last_name' => null,
            'username' => null,
            'language_code' => null,
            'is_premium' => false,
            'allows_write_to_pm' => false,
            'role' => 'user',
            'telegram_auth_date' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a Telegram user.
     */
    public function telegram(): static
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $username = fake()->userName();
        
        return $this->state(fn (array $attributes) => [
            'telegram_id' => fake()->unique()->numberBetween(100000, 999999999),
            'name' => $firstName . ' ' . $lastName,
            'email' => $username . '@telegram.local',
            'password' => '',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'language_code' => fake()->randomElement(['en', 'es', 'fr', 'de', 'ru']),
            'is_premium' => fake()->boolean(20), // 20% chance of premium
            'allows_write_to_pm' => fake()->boolean(70), // 70% chance of allowing PMs
            'telegram_auth_date' => now(),
        ]);
    }
}
