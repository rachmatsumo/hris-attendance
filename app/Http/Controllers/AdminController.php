<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\Position;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\User;
use App\Models\WorkSchedule;

class AdminController extends Controller
{
    public function index()
    {
        // Logic for admin dashboard or menu
        return view('admin.menu');
    }

    public function departmentList()
    {
        $departments = Department::orderBy('name')->paginate(5);

        return view('admin.master_data.department_list', compact('departments'));
    }

    public function positionList()
    {
        // Logic to list departments
        $positions = Position::with(['department', 'salary'])->paginate(10);
        
        return view('admin.master_data.position_list', compact('positions')); 
    }

    public function employeeList()
    {
        $employees = User::orderBy('name')->paginate(10);

        return view('admin.master_data.employee_list', compact('employees')); 
    }

    public function holidayList()
    {
        $holidays = Holiday::orderBy('name')->paginate(10);

        return view('admin.master_data.holiday_list', compact('holidays'));  
    }

    public function workLocation()
    {
        $work_locations = Location::orderBy('name')->paginate(10);

        return view('admin.master_data.work_location', compact('work_locations'));  
    }

    public function settings()
    {
        // Logic to list departments
        return view('admin.master_data.settings');
    }

    public function workSchedule()
    { 
        $work_schedules = WorkSchedule::with(['user'])->paginate(10);

        return view('admin.resource_management.work_schedule', compact('work_schedules'));   
    }

    public function recapAttendance()
    {
        // Logic to list departments
        $recap_attendances = Attendance::orderBy('date')->paginate(10);

        return view('admin.resource_management.recap_attendance', compact('recap_attendances'));   
    }

    public function payroll()
    {
        // Logic to list departments
        return view('admin.resource_management.payroll');
    }
 
}
