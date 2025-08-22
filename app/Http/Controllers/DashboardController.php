<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Attendance; 
use App\Models\WorkSchedule;

class DashboardController extends Controller
{

    private function applyUserScope($query)
    {
        $user = Auth::user();
        // $isHrOrAdmin = in_array($user->role, ['employee']);
        $isHrOrAdmin = in_array($user->role, ['hr', 'admin']);

        if (!$isHrOrAdmin) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function index()
    {
        $now      = Carbon::now();
        $month = date('Y-m');
        $year = date('Y', strtotime($month));
        $monthNum = date('m', strtotime($month));
        $lastMonth = $now->copy()->subMonth();

        $schedules = WorkSchedule::with('workingTime')
            ->where('user_id', Auth::id())
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $monthNum)
            ->get();

        $dates = $schedules->pluck('work_date')->unique()->sort()->values(); 

        if ($dates->isEmpty()) {
            $year = date('Y', strtotime($month));   
            $monthNum = date('m', strtotime($month));

            $start = Carbon::create($year, $monthNum, 1);
            $end = $start->copy()->endOfMonth();

            $period = CarbonPeriod::create($start, $end);

            $dates = collect();
            foreach ($period as $date) {
                $dates->push($date->format('Y-m-d'));
            }
        }

        $data = [
            'total_schedules' => 245,
            'total_attendance' => 198,
            'total_late' => 32,
            'total_absent' => 15,
            'total_employees' => 127,
            'pending_leaves' => 8,
            'attendance_rate' => 94.2,
            'departments' => [],
            'weekly_data' => [],
            'recent_activities' => []
        ];

        $attendances = Attendance::with('user')
                        ->orderBy('id', 'desc')
                        ->take(5)
                        ->get();

        $totalWorkSchedules = $this->applyUserScope(WorkSchedule::whereMonth('work_date', Carbon::now()->month)
                                    ->whereYear('work_date', Carbon::now()->year)
                                    ->whereNotNull('working_time_id')
        )->count();

        $totalPresent = $this->applyUserScope(Attendance::whereMonth('date', Carbon::now()->month)
                                    ->whereYear('date', Carbon::now()->year)
                                    ->where('status', 'present')
        )->count();

        $totalLate = $this->applyUserScope(Attendance::whereMonth('date', Carbon::now()->month)
                                    ->whereYear('date', Carbon::now()->year)
                                    ->where('status', 'late')
        )->count();
 
        $absent = $this->applyUserScope(WorkSchedule::whereMonth('work_date', Carbon::now()->month)
                                ->whereYear('work_date', Carbon::now()->year)
                                ->whereDay('work_date', '<=', Carbon::now()->day)
                                ->whereDoesntHave('attendance') 
        )->count();


        $lastTotalWorkSchedules = $this->applyUserScope(WorkSchedule::whereMonth('work_date', $lastMonth->month)
            ->whereYear('work_date', $lastMonth->year)
            ->whereNotNull('working_time_id')
        )->count();

        $lastTotalPresent = $this->applyUserScope(Attendance::whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->where('status', 'present')
        )->count();

        $lastTotalLate = $this->applyUserScope(Attendance::whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->where('status', 'late')
        )->count();

        $lastAbsent = $this->applyUserScope(WorkSchedule::whereMonth('work_date', $lastMonth->month)
            ->whereYear('work_date', $lastMonth->year)
            ->whereDay('work_date', '<=', min($now->day, $lastMonth->daysInMonth)) // biar fair sampai tanggal yg sama
            ->whereDoesntHave('attendance')
        )->count(); 
            
        function growthPercent($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $previous) / $previous) * 100, 2);
        } 

        $statistics = (OBJECT) [
            'total_schedule'=> $totalWorkSchedules,
            'total_present' => $totalPresent,
            'total_late'    => $totalLate,
            'total_absent'  => $absent,
            'growth_schedule'=> growthPercent($totalWorkSchedules, $lastTotalWorkSchedules),
            'growth_present'=> growthPercent($totalPresent, $lastTotalPresent),
            'growth_late'   => growthPercent($totalLate, $lastTotalLate),
            'growth_absent' => growthPercent($absent, $lastAbsent),
        ];
        // dd($totalWorkSchedules, $totalPresent, $totalLate, $absent);

        return view('dashboard.dashboard', compact('schedules', 'dates', 'data', 'attendances', 'statistics'));
    }
}
