<?php

namespace Database\Seeders;

use App\Models\League;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $premierLeague = League::factory()->create(['name' => 'Premier League', 'slug' => 'premier-league', 'country' => 'England', 'image' => 'Premier League.png']);
        $laLiga = League::factory()->create(['name' => 'La Liga', 'slug' => 'la-liga', 'country' => 'Spain', 'image' => 'La Liga.png']);
        $serieA = League::factory()->create(['name' => 'Serie A', 'slug' => 'serie-a', 'country' => 'Italy', 'image' => 'Serie A.png']);
        $bundesLeague = League::factory()->create(['name' => 'Bundes League', 'slug' => 'bundes-league', 'country' => 'Germany', 'image' => 'Bundes League.png']);
        $ligue1 = League::factory()->create(['name' => 'Ligue 1', 'slug' => 'ligue-1', 'country' => 'France', 'image' => 'Ligue 1.png']);
    }
}
