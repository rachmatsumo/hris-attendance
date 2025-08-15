<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSchedule;

class WorkScheduleController extends Controller
{
    public function index()
    { 
        $work_schedules = WorkSchedule::with(['user'])->paginate(10);

        return view('admin.resource_management.work_schedule', compact('work_schedules'));   
    }
}
