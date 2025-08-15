<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;

class PayrollController extends Controller
{
    public function index()
    {
        // Logic to list departments
        return view('admin.resource_management.payroll');
    }
}
