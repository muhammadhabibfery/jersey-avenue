<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = User::$roles;

        foreach ($roles as $role)
            $user[strtolower($role)] = User::factory()->create(['role' => $role]);

        $this->call([
            RegionSeeder::class,
            LeagueSeeder::class,
            JerseySeeder::class
        ]);
    }
}
