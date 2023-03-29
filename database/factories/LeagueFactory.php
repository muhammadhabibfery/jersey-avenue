<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
class LeagueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(5, true);

        return [
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'country' => fake()->country(),
            'image' => fake()->word(),
            'created_by' => 1
        ];
    }
}
