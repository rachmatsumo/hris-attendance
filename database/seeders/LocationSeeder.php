<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Sepatan',
                'lat_long' => '-6.1076327,106.5565591', 
                'is_active' => true,
                'radius' => 5,
            ], 
            [
                'name' => 'Tangerang Kota',
                'lat_long' => '-6.1871047,106.6309931', 
                'is_active' => true,
                'radius' => 5,
            ], 
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
