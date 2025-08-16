<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\WorkingTime;
use Carbon\Carbon;

class WorkScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $bulk_id = md5(now());
        $users = User::whereIn('role', ['employee', 'hr'])->get();

        // Ambil semua shift
        $shifts = WorkingTime::whereIn('name', [
            'Shift Pagi', 'Shift Siang', 'Shift Malam', 'Office Hour'
        ])->pluck('id', 'name');

        $shiftPagi     = $shifts['Shift Pagi'] ?? null;
        $shiftSiang    = $shifts['Shift Siang'] ?? null;
        $shiftMalam    = $shifts['Shift Malam'] ?? null;
        $officeHour    = $shifts['Office Hour'] ?? null;

        $startDate = Carbon::create(2025, 8, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        foreach ($users as $user) {
            $insertData = [];
            $date = $startDate->copy();

            while ($date->lte($endDate)) {

                if ($user->role === 'hr') {
                    // Untuk HR: Senin (1) s/d Jumat (5) -> Office Hour
                    $workingTimeId = ($date->dayOfWeekIso >= 1 && $date->dayOfWeekIso <= 5) ? $officeHour : null;
                } else {
                    // Untuk employee: Pola 5 hari: Pagi → Siang → Malam → Libur → Libur
                    $dayOfCycle = ($date->day - 1) % 5;
                    $workingTimeId = match ($dayOfCycle) {
                        0 => $shiftPagi,
                        1 => $shiftSiang,
                        2 => $shiftMalam,
                        default => null,
                    };
                }

                $insertData[] = [
                    'user_id'         => $user->id,
                    'work_date'       => $date->toDateString(),
                    'working_time_id' => $workingTimeId,
                    'is_active'       => $workingTimeId !== null,
                    'bulk_id'         => $bulk_id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];

                $date->addDay();
            }

            WorkSchedule::insert($insertData);
        }
    }
}
