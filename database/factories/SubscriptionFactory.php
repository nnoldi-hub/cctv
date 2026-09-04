<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'plan' => fake()->randomElement(['Mentenanta de baza', 'Mentenanta premium']),
            'price' => fake()->randomFloat(2, 50, 500),
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'started_at' => now()->subMonths(fake()->numberBetween(0, 12)),
            'next_billing_at' => now()->addMonth(),
        ];
    }
}
