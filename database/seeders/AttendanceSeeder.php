<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $workSchedules = WorkSchedule::with('workingTime')
            ->where('is_active', true)
            ->get();

        foreach ($workSchedules as $schedule) {
            // Random hadir: 80%
            if (rand(1, 100) <= 80) {
                $shift = $schedule->workingTime;
                if (!$shift) continue;

                $workDate = Carbon::parse($schedule->work_date);

                // Jam masuk resmi
                $startTime = Carbon::parse($workDate->toDateString().' '.$shift->start_time);

                // Jam pulang resmi (antisipasi shift lintas hari)
                $endTime = Carbon::parse($workDate->toDateString().' '.$shift->end_time);
                if ($endTime->lessThanOrEqualTo($startTime)) {
                    $endTime->addDay(); // shift malam
                }

                // Clock in
                $lateTolerance  = (int) ($shift->late_tolerance_minutes ?? 0);
                $clockInOffset  = rand(-5, 5); // ±5 menit
                $isLate         = rand(0, 100) < 30; // 30% telat

                $clockInTime = safeAddMinutes(
                    $startTime->copy(),
                    $isLate ? $lateTolerance + rand(1, 10) : $clockInOffset
                );

                // Clock out random ±10 menit
                $clockOutTime = safeAddMinutes($endTime->copy(), rand(-10, 10));

                // Hitung jam kerja aktual
                $workingMinutesActual = (float) $clockOutTime->diffInMinutes($clockInTime);
                $workingHoursActual   = round($workingMinutesActual / 60, 2);

                // Hitung keterlambatan
                $graceTime   = safeAddMinutes($startTime->copy(), $lateTolerance);
                $lateMinutes = $clockInTime->greaterThan($graceTime)
                    ? (float) $graceTime->diffInMinutes($clockInTime)
                    : 0;

                // Hitung jam kerja shift efektif (dikurangi break)
                $breakStart = $shift->break_start_time
                    ? Carbon::parse($workDate->toDateString().' '.$shift->break_start_time)
                    : null;

                $breakEnd = $shift->break_end_time
                    ? Carbon::parse($workDate->toDateString().' '.$shift->break_end_time)
                    : null;

                $workDuration  = (float) $endTime->diffInMinutes($startTime);
                $breakDuration = $breakStart && $breakEnd
                    ? (float) $breakEnd->diffInMinutes($breakStart)
                    : 0;
                $effectiveHours = round(($workDuration - $breakDuration) / 60, 2);

                // Tentukan rate
                $isHoliday = Holiday::whereDate('date', $workDate)->where('is_active', 1)->exists();
                $isWeekend = $workDate->isWeekend();

                if ($isHoliday) {
                    $rate  = (float) setting('holiday_overtime_rate');
                    $notes = 'Holiday';
                } elseif ($isWeekend) {
                    $rate  = (float) setting('weekend_overtime_rate');
                    $notes = 'Weekend';
                } else {
                    $rate  = 0;
                    $notes = 'Weekday';
                }

                $overtimeSalary = abs($effectiveHours * $rate);

                // Insert attendance
                Attendance::create([
                    'user_id'          => $schedule->user_id,
                    'work_schedule_id' => $schedule->id,
                    'date'             => $schedule->work_date,
                    'clock_in_time'    => $clockInTime->toDateTimeString(),
                    'clock_in_lat'     => rand(-90000000, 90000000)/1000000,
                    'clock_in_lng'     => rand(-180000000, 180000000)/1000000,
                    'clock_out_time'   => $clockOutTime->toDateTimeString(),
                    'clock_out_lat'    => rand(-90000000, 90000000)/1000000,
                    'clock_out_lng'    => rand(-180000000, 180000000)/1000000,
                    'status'           => $lateMinutes > 0 ? 'late' : 'present',
                    'working_hours'    => $workingHoursActual,
                    'late_minutes'     => $lateMinutes,
                    'overtime_salary'  => $overtimeSalary,
                    'admin_notes'      => $notes,
                ]);
            }
        }
    }
}
