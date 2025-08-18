<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\WorkSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        $month = date('Y-m');
        $year = date('Y', strtotime($month));
        $monthNum = date('m', strtotime($month));

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

        return view('dashboard.dashboard', compact('schedules', 'dates'));
    }
}
