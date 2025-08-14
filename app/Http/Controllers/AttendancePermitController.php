<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendancePermitController extends Controller
{
    public function index()
    {
        // Logic to display attendance permits
        return view('attendance_permits.index');
    }
}
