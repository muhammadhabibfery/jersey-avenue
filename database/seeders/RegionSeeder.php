<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use App\Services\Facades\Shipping;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = Shipping::province()->get();

        foreach ($provinces as $province) {
            $provinceResult = Province::create(['name' => $province['province']]);

            $cites = Shipping::city()->fromProvince($province['province_id'])->get();
            foreach ($cites as $city)
                City::create([
                    'province_id' => $provinceResult->id,
                    'name' => $city['city_name'],
                    'type' => $city['type'],
                    'postal_code' => $city['postal_code']
                ]);
        }
    }
}
