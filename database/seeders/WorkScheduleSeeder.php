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
        $users = User::whereIn('role', ['employee', 'hr'])->get();

        // Ambil semua shift
        $shifts = WorkingTime::whereIn('name', [
            'Shift Pagi', 'Shift Siang', 'Shift Malam', 'Office Hour 1', 'Office Hour 2'
        ])->pluck('id', 'name');

        $shiftPagi     = $shifts['Shift Pagi'] ?? null;
        $shiftSiang    = $shifts['Shift Siang'] ?? null;
        $shiftMalam    = $shifts['Shift Malam'] ?? null;
        $officeHour1   = $shifts['Office Hour 1'] ?? null;
        $officeHour2   = $shifts['Office Hour 2'] ?? null;

        $startDate = Carbon::now()->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Pola shift per grup: P = Pagi, S = Siang, M = Malam, L = Libur
        $shiftPatterns = [
            1 => ['P', 'S', 'M', 'L'],
            2 => ['S', 'M', 'L', 'P'],
            3 => ['M', 'L', 'P', 'S'],
            4 => ['L', 'P', 'S', 'M'],
        ];

        foreach ($users as $index => $user) {
            $insertData = [];
            $date = $startDate->copy();

            if ($user->role === 'hr') {
                $bulk_id = md5(now().'hr');

                while ($date->lte($endDate)) {
                    // HR: Senin s/d Kamis -> Office Hour 1, Jumat -> Office Hour 2, Sabtu & Minggu libur
                    if ($date->dayOfWeekIso >= 1 && $date->dayOfWeekIso <= 4) {
                        $workingTimeId = $officeHour1;
                    } elseif ($date->dayOfWeekIso === 5) {
                        $workingTimeId = $officeHour2;
                    } else {
                        $workingTimeId = null;
                    }
                    if ($date->month === $startDate->month) {
                        $insertData[] = [
                            'user_id'         => $user->id,
                            'work_date'       => $date->toDateString(),
                            'working_time_id' => $workingTimeId,
                            'is_active'       => $workingTimeId !== null,
                            'bulk_id'         => $bulk_id,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                    $date->addDay();
                }
            } else {
                $group = ($index % 4) + 1; // distribusi employee ke grup 1-4
                $pattern = $shiftPatterns[$group];
                $bulk_id = md5(now().'employee_group'.$group); // bulk_id per grup
                $totalDays = $startDate->diffInDays($endDate) + 1;

                for ($i = 0; $i < $totalDays; $i++) {
                    $currentDate = $startDate->copy()->addDays($i);
                    if ($currentDate->month !== $startDate->month) {
                        continue; // skip tanggal di luar bulan ini
                    }
                    
                    $shiftCode = $pattern[$i % 4]; // ambil shift sesuai pattern
                    $workingTimeId = match($shiftCode) {
                        'P' => $shiftPagi,
                        'S' => $shiftSiang,
                        'M' => $shiftMalam,
                        'L' => null,
                    };

                    $insertData[] = [
                        'user_id'         => $user->id,
                        'work_date'       => $currentDate->toDateString(),
                        'working_time_id' => $workingTimeId,
                        'is_active'       => $workingTimeId !== null,
                        'bulk_id'         => $bulk_id,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            WorkSchedule::insert($insertData);
        }
    }
}
