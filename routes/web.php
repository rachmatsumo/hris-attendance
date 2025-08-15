<?php

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckRole;
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
    
    Route::resource('account', AccountController::class)->only(['update']);
    Route::get('account/menu', [AccountController::class, 'index'])->name('account.index');
    Route::get('account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    
    // Absensi
    Route::resource('attendances', AttendanceController::class)->only(['index', 'store', 'update']);
    
    // Izin Absensi / Leave Requests
    Route::resource('permits', AttendancePermitController::class)->only(['index', 'create', 'store', 'update']);

});

Route::middleware(['auth', CheckRole::class.':admin,hr'])->group(function () {
    require __DIR__.'/admin.php';
});

require __DIR__.'/auth.php';

Auth::routes(); 
