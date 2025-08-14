<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkSchedule;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();

        foreach ($employees as $employee) {
            // Monday to Friday (1-5)
            for ($day = 1; $day <= 5; $day++) {
                WorkSchedule::create([
                    'user_id' => $employee->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'break_start_time' => '12:00:00',
                    'break_end_time' => '13:00:00',
                    'late_tolerance_minutes' => 15,
                    'is_active' => true,
                ]);
            }

            // Saturday (6) - Half day
            WorkSchedule::create([
                'user_id' => $employee->id,
                'day_of_week' => 6,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'break_start_time' => null,
                'break_end_time' => null,
                'late_tolerance_minutes' => 10,
                'is_active' => true,
            ]);
        }

        // HR and Admin with different schedule
        $hrUsers = User::whereIn('role', ['admin', 'hr'])->get();
        
        foreach ($hrUsers as $user) {
            // Monday to Friday
            for ($day = 1; $day <= 5; $day++) {
                WorkSchedule::create([
                    'user_id' => $user->id,
                    'day_of_week' => $day,
                    'start_time' => '07:30:00',
                    'end_time' => '16:30:00',
                    'break_start_time' => '12:00:00',
                    'break_end_time' => '13:00:00',
                    'late_tolerance_minutes' => 10,
                    'is_active' => true,
                ]);
            }
        }
    }
}
