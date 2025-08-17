<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $workSchedules = WorkSchedule::with('workingTime')->where('is_active', true)->get();

        foreach ($workSchedules as $schedule) {
            // Random: 80% hadir, 20% tidak hadir
            if (rand(1, 100) <= 80) {
                $shift = $schedule->workingTime;

                if (!$shift) {
                    continue; // aman, kalau tidak ada workingTime
                }

                $workDate = Carbon::parse($schedule->work_date);

                // Jam masuk ideal
                $startTime = Carbon::parse($workDate->toDateString() . ' ' . $shift->start_time);

                // Random clock_in_time
                $lateTolerance = $shift->late_tolerance_minutes ?? 0;
                $clockInOffset = rand(-5, 5); // ±5 menit random
                $isLate = rand(0, 100) < 30; // 30% kemungkinan telat
                $clockInTime = $startTime->copy()->addMinutes($isLate ? $lateTolerance + rand(1, 10) : $clockInOffset);

                // Jam pulang random
                $endTime = Carbon::parse($workDate->toDateString() . ' ' . $shift->end_time);
                $clockOutTime = $endTime->copy()->addMinutes(rand(-10, 10));

                Attendance::create([
                    'user_id'         => $schedule->user_id,
                    'work_schedule_id'=> $schedule->id,
                    'date'            => $schedule->work_date,
                    'clock_in_time'   => $clockInTime->toTimeString(),
                    'clock_in_lat'    => rand(-90000000, 90000000)/1000000, // random lat
                    'clock_in_lng'    => rand(-180000000, 180000000)/1000000, // random lng
                    'clock_in_photo'  => null,
                    'clock_in_notes'  => null,
                    'clock_out_time'  => $clockOutTime->toTimeString(),
                    'clock_out_lat'   => rand(-90000000, 90000000)/1000000,
                    'clock_out_lng'   => rand(-180000000, 180000000)/1000000,
                    'clock_out_photo' => null,
                    'clock_out_notes' => null,
                    'status'          => $isLate ? 'late' : 'present',
                ]);
            }
            // Kalau tidak hadir, tidak insert apa-apa (absent)
        }
    }
}
