<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDateTime = fake()->dateTimeBetween('now', '+30 days');
        $endDateTime = clone $startDateTime;
        $endDateTime->add(new \DateInterval('PT' . fake()->randomElement([30, 45, 60, 90, 120]) . 'M'));

        return [
            'user_id' => User::factory(),
            'provider_id' => Provider::factory(),
            'service_id' => Service::factory(),
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime,
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
