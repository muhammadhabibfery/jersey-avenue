<?php

namespace Database\Seeders;

use App\Models\Jersey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JerseySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $livHome = Jersey::factory()->create([
            'name' => 'FC Liverpool 2022-2023 Home Jersey',
            'slug' => 'fc-liverpool-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'liv 22-23 home.png',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $livAway = Jersey::factory()->create([
            'name' => 'FC Liverpool 2022-2023 Away Jersey',
            'slug' => 'fc-liverpool-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'liv 22-23 away.png',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $manCityHome = Jersey::factory()->create([
            'name' => 'Manchester City 2022-2023 Home Jersey',
            'slug' => 'manchester-city-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'man city 22-23 home.png',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $manCityAway = Jersey::factory()->create([
            'name' => 'Manchester City 2022-2023 Away Jersey',
            'slug' => 'manchester-city-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'man city 22-23 away.png',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $arsenalHome = Jersey::factory()->create([
            'name' => 'Arsenal 2022-2023 Home Jersey',
            'slug' => 'arsenal-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'arsenal 22-23 home.png',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $arsenalAway = Jersey::factory()->create([
            'name' => 'Arsenal 2022-2023 Away Jersey',
            'slug' => 'arsenal-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'arsenal 22-23 away.png',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $barcaHome = Jersey::factory()->create([
            'name' => 'FC Barcelona 2022-2023 Home Jersey',
            'slug' => 'fc-barcelona-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'fcb 22-23 home.png',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $barcaAway = Jersey::factory()->create([
            'name' => 'FC Barcelona 2022-2023 Away Jersey',
            'slug' => 'fc-barcelona-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'fcb 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $madridHome = Jersey::factory()->create([
            'name' => 'Real Madrid 2022-2023 Home Jersey',
            'slug' => 'real-madrid-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'rma 22-23 home.png',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $madridAway = Jersey::factory()->create([
            'name' => 'Real Madrid 2022-2023 Away Jersey',
            'slug' => 'real-madrid-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'rma 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $juveHome = Jersey::factory()->create([
            'name' => 'Juventus 2022-2023 Home Jersey',
            'slug' => 'juventus-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'juv 22-23 home.jpg',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $juveAway = Jersey::factory()->create([
            'name' => 'Juventus 2022-2023 Away Jersey',
            'slug' => 'juventus-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'juv 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $acMilanHome = Jersey::factory()->create([
            'name' => 'AC Milan 2022-2023 Home Jersey',
            'slug' => 'ac-milan-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'acm 22-23 home.jpg',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $acMilanAway = Jersey::factory()->create([
            'name' => 'AC Milan 2022-2023 Away Jersey',
            'slug' => 'ac-milan-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'acm 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $munichHome = Jersey::factory()->create([
            'name' => 'FC Bayern Munich 2022-2023 Home Jersey',
            'slug' => 'fc-bayern-munich-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'fbm 22-23 home.jpg',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $munichAway = Jersey::factory()->create([
            'name' => 'FC Bayern Munich 2022-2023 Away Jersey',
            'slug' => 'fc-bayern-munich-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'fbm 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $dortmundHome = Jersey::factory()->create([
            'name' => 'Borussia Dortmund 2022-2023 Home Jersey',
            'slug' => 'borussia-dortmund-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'bvb 22-23 home.jpg',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $dortmundAway = Jersey::factory()->create([
            'name' => 'Borussia Dortmund 2022-2023 Away Jersey',
            'slug' => 'borussia-dortmund-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'bvb 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);

        $psgHome = Jersey::factory()->create([
            'name' => 'PSG 2022-2023 Home Jersey',
            'slug' => 'psg-2022-2023-home-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'psg 22-23 home.jpg',
            'stock' => ['S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
        $psgAway = Jersey::factory()->create([
            'name' => 'PSG 2022-2023 Away Jersey',
            'slug' => 'psg-2022-2023-away-jersey',
            'type' => 'Original',
            'weight' => 500,
            'price' => 750000,
            'price_nameset' => 50000,
            'image' => 'psg 22-23 away.jpg',
            'stock' => ['S' => 0, 'M' => 2, 'L' => 3, 'XL' => 4]
        ]);
    }
}
