<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'google_id' => null,
            'city_id' => null,
            'name' => $name,
            'username' => str($name)->slug('')->value(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08' . rand(11111111111, 99999999999),
            'role' => fake()->randomElement(User::$roles),
            'address' => fake()->address(),
            'status' => User::$status[0],
            'avatar' => null,
            'password' => Hash::make('abc@123123'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_by' => null,
            'updated_by' => null,
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
}
