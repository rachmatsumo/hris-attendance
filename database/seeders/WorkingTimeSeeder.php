<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkingTime;
use Illuminate\Support\Facades\DB;

class WorkingTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('working_times')->insert([
            [
                'name' => 'Shift Pagi',
                'code' => 'SF-PG',
                'start_time' => '07:00',
                'end_time' => '15:00',
                'end_next_day' => false,
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
                // 'is_location_limited' => true,
                'color' => '#eeeee4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shift Siang',
                'code' => 'SF-SG',
                'start_time' => '15:00',
                'end_time' => '23:00',
                'end_next_day' => false,
                'break_start_time' => '18:00',
                'break_end_time' => '19:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
                // 'is_location_limited' => true,
                'color' => '#FFFD89',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shift Malam',
                'code' => 'SF-ML',
                'start_time' => '23:00',
                'end_time' => '07:00',
                'end_next_day' => true, // selesai besok
                'break_start_time' => '03:00',
                'break_end_time' => '04:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
                // 'is_location_limited' => true,
                'color' => '#E89AD6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Hour 1',
                'code' => 'OH-SK',
                'start_time' => '07:00',
                'end_time' => '17:00',
                'end_next_day' => false,
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
                // 'is_location_limited' => false,
                'color' => '#96d3d6ff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Hour 2',
                'code' => 'OH-JJ',
                'start_time' => '07:00',
                'end_time' => '15:30',
                'end_next_day' => false,
                'break_start_time' => '12:00',
                'break_end_time' => '13:00',
                'late_tolerance_minutes' => 15,
                'is_active' => true,
                // 'is_location_limited' => false,
                'color' => '#52df91ff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
