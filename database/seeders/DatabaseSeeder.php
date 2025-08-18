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
            SettingSeeder::class,
            LevelSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            UserSeeder::class,
            HolidaySeeder::class,
            LocationSeeder::class, 
            IncomeSeeder::class,
            DeductionSeeder::class,
            WorkingTimeSeeder::class,
            WorkScheduleSeeder::class,
            AttendanceSeeder::class, 
            AttendancePermitSeeder::class, 
            PayrollSeeder::class, 
        ]);
    }
}
