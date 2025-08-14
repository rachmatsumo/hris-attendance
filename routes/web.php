<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendancePermitController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    // Route::get('/akun', [AkunController::class, 'edit'])->name('akun.edit');
    // Route::patch('/akun', [AkunController::class, 'update'])->name('akun.update');
    // Route::delete('/akun', [AkunController::class, 'destroy'])->name('akun.destroy');
    Route::resource('account', AccountController::class)->only(['update']);
    Route::get('account/menu', [AccountController::class, 'index'])->name('account.index');
    Route::get('account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    
    Route::get('/admin/menu', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/department', [AdminController::class, 'departmentList'])->name('admin.department');
    Route::get('/admin/position', [AdminController::class, 'positionList'])->name('admin.position');
    Route::get('/admin/karyawan', [AdminController::class, 'karyawanList'])->name('admin.karyawan');
    Route::get('/admin/setting', [AdminController::class, 'settings'])->name('admin.setting');
    
    Route::get('/admin/rekap-absensi', [AdminController::class, 'rekapAbsensi'])->name('admin.rekap-absensi');
    Route::get('/admin/jadwal-kerja', [AdminController::class, 'jadwalKerja'])->name('admin.jadwal-kerja');
    Route::get('/admin/payroll', [AdminController::class, 'payroll'])->name('admin.payroll');
    
    // Absensi
    Route::resource('attendances', AttendanceController::class)->only(['index', 'store', 'update']);
    
    // Izin Absensi
    Route::resource('permits', AttendancePermitController::class)->only(['index', 'create', 'store', 'update']);
    
    // Cuti
    Route::resource('leaves', LeaveRequestController::class)->only(['index', 'create', 'store', 'update']);
    
    // Payroll
    Route::resource('payrolls', PayrollController::class)->only(['index', 'show']);
    
    // Setting
    Route::resource('settings', SettingController::class)->only(['index', 'update']);
});


require __DIR__.'/auth.php';

Auth::routes(); 
