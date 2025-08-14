<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;

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
        $positions = Position::with(['department', 'salary'])->paginate(5);;
        
        return view('admin.master_data.position_list', compact('positions')); 
    }

    public function karyawanList()
    {
        $karyawans = User::orderBy('name')->paginate(5);

        return view('admin.master_data.karyawan_list', compact('karyawans')); 
    }

    public function settings()
    {
        // Logic to list departments
        return view('admin.master_data.settings');
    }

    public function jadwalKerja()
    {
        // Logic to list departments
        return view('admin.resource_management.jadwal_kerja');
    }

    public function rekapAbsensi()
    {
        // Logic to list departments
        return view('admin.resource_management.rekap_absensi');
    }

    public function payroll()
    {
        // Logic to list departments
        return view('admin.resource_management.payroll');
    }
 
}
