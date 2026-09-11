<?php

namespace Database\Factories;

use App\Models\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopOrder>
 */
class ShopOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'SHOP-'.now()->format('Y').'-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
            'shipping_city' => fake()->city(),
            'payment_method' => 'cod',
            'subtotal' => 100,
            'discount_total' => 0,
            'manual_discount' => 0,
            'shipping_cost' => 25,
            'total' => 125,
            'status' => 'new',
        ];
    }
}
