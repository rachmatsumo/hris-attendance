<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendancePermit;
use App\Models\WorkSchedule;
use App\Exports\DailyRecapAttendanceExport;
use App\Exports\MonthlyRecapAttendanceWithSummaryExport;
use App\Exports\MonthlyRecapAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class RecapAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily');

        if ($type == 'monthly') {
            $selectPeriod = $request->get('select_period') ?? date('Y-m');
            $month = date('m', strtotime($selectPeriod));
            $year = date('Y', strtotime($selectPeriod));

            $recap_attendances = WorkSchedule::with([
                    'user.department',
                    'user.position',
                    'workingTime',
                    'attendance' => function($q) use ($month, $year) {
                        $q->whereMonth('date', $month)
                        ->whereYear('date', $year);
                    },
                    'user.attendancePermits' => function($q) use ($month, $year) {
                        $start = date("$year-$month-01");
                        $end = date("$year-$month-t");
                        $q->where('status', 'approved')
                        ->where(function($query) use ($start, $end) {
                            $query->whereBetween('start_date', [$start, $end])
                                    ->orWhereBetween('end_date', [$start, $end])
                                    ->orWhere(function($q2) use ($start, $end) {
                                        $q2->where('start_date', '<=', $start)
                                        ->where('end_date', '>=', $end);
                                    });
                        });
                    }
                ])
                ->whereMonth('work_date', $month)
                ->whereYear('work_date', $year)
                ->whereNotNull('working_time_id')
                ->orderBy('work_date')
                ->paginate(10);

        } else {
            $selectPeriod = $request->get('select_period', date('Y-m-d'));

            $recap_attendances = WorkSchedule::with([
                    'user.department',
                    'user.position',
                    'workingTime',
                    'attendance' => function($q) use ($selectPeriod) {
                        $q->whereDate('date', $selectPeriod);
                    },
                    'user.attendancePermits' => function($q) use ($selectPeriod) {
                        $q->where('status', 'approved')
                        ->whereDate('start_date', '<=', $selectPeriod)
                        ->whereDate('end_date', '>=', $selectPeriod);
                    }
                ])
                ->whereDate('work_date', $selectPeriod)
                ->whereNotNull('working_time_id')
                ->orderBy('work_date')
                ->paginate(10);
        }
        // dd($recap_attendances);
       $recap_attendances->getCollection()->transform(function($schedule) {
            if($schedule->attendance) {
                $schedule->status = $schedule->attendance->status;
            } else { 
                $permit = $schedule->user->attendancePermits()
                                ->where('status', 'approved')
                                ->whereDate('start_date', '<=', $schedule->work_date)
                                ->whereDate('end_date', '>=', $schedule->work_date)
                                ->first();
                $schedule->status = $permit ? 'Izin' : 'Tidak Hadir';
            }
            return $schedule;
        });

        // dd($recap_attendances);

        $recap_attendances = $recap_attendances->appends([
            'type' => $type,
            'select_period' => $selectPeriod
        ]);

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
        return Excel::download(new MonthlyRecapAttendanceWithSummaryExport($month), "Monthly_Recap_Attendance_$name.xlsx");
    } 
}
