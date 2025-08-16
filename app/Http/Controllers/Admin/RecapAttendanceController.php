<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Exports\DailyRecapAttendanceExport;
use App\Exports\MonthlyRecapAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class RecapAttendanceController extends Controller
{
    public function index(Request $request)
    { 

        $type = $request->get('type', 'daily');

        if($type == 'monthly') {
            $selectPeriod = $request->get('select_period') ?? date('Y-m');
            $recap_attendances = Attendance::with('workSchedule')
                ->whereMonth('date', date('m', strtotime($selectPeriod)))
                ->whereYear('date', date('Y', strtotime($selectPeriod)))
                ->paginate(10);
        } else {
            $selectPeriod = $request->get('select_period', date('Y-m-d')) ?? date('Y-m-d');
            $recap_attendances = Attendance::with('workSchedule')
                ->whereDate('date', $selectPeriod)
                ->paginate(10);
        }  
        return view('admin.resource_management.recap_attendance', compact('recap_attendances', 'selectPeriod'));   
    }

    public function exportDaily(Request $request)
    {
        $date = $request->select_period ?? date('Y-m-d');  

        $timestamp = now()->format('dHis');

        $name = $date . '__' . $timestamp;
        return Excel::download(new DailyRecapAttendanceExport($date), "Daily_Attendance_$name.xlsx");
    } 

    public function exportMonthly(Request $request)
    {
        $month = $request->select_period ?? date('Y-m');  

        $timestamp = now()->format('dHis');

        $name = $month . '__' . $timestamp;
        return Excel::download(new MonthlyRecapAttendanceExport($month), "Monthly_Attendance_$name.xlsx");
    } 
}
