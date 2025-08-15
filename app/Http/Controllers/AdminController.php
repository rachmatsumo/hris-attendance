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
 
}
