<?php

namespace Database\Factories;

use App\Models\Jersey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jersey>
 */
class JerseyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(4, true);

        return [
            'league_id' => fake()->randomElement([1, 2, 3, 4, 5]),
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'type' => fake()->words(3, true),
            'weight' => rand(100, 999),
            'price' => 250000,
            'price_nameset' => 50000,
            'image' => fake()->word(),
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4],
            'created_by' => 1
        ];
    }
}
