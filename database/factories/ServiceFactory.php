<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->sentences(2, true),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'display_price' => '$' . fake()->numberBetween(25, 200) . '.00',
        ];
    }
}