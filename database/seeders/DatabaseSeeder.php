<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    { 
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            UserSeeder::class,
            WorkScheduleSeeder::class,
            HolidaySeeder::class,
            SettingSeeder::class,
            LocationSeeder::class,
            SalariesSeeder::class,
        ]);
    }
}
