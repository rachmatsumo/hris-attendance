<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class RecapAttendanceController extends Controller
{
    public function index()
    {
        // Logic to list departments
        $recap_attendances = Attendance::orderBy('date')->paginate(10);

        return view('admin.resource_management.recap_attendance', compact('recap_attendances'));   
    }
}
