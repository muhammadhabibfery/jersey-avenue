<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => rand(1, 10),
            // 'user_id' => User::factory(),
            'invoice_number' => generateInvoiceNumber(),
            'total_price' => 1000000,
            'courier_services' => null,
            'status' => fake()->randomElement(Order::$status)
        ];
    }
}
