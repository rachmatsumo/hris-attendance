<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $employees = User::orderBy('name')->paginate(10);

        return view('admin.master_data.employee_list', compact('employees')); 
    }

    
}
