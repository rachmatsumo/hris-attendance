<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
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
    Route::resource('account', AccountController::class)->only(['index', 'store', 'edit', 'update']);

    Route::get('account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    
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
