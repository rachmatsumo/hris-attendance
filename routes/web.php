<?php

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\AccountController; 
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendancePermitController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;


Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

Route::middleware('auth')->group(function () {
    
    Route::get('account/menu', [AccountController::class, 'index'])->name('account.index');
    Route::get('account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');
    Route::get('account/payroll', [AccountController::class, 'payrollIndex'])->name('account.payroll');
    Route::get('account/setting', [AccountController::class, 'setting'])->name('account.setting'); 
    Route::post('account/save-fcm-token', [AccountController::class, 'saveFcmToken'])->name('account.save-fcm-token'); 
    Route::post('account/remove-fcm-token', [AccountController::class, 'removeFcmToken'])->name('account.remove-fcm-token'); 
    Route::resource('account', AccountController::class)->only(['update']);
    
    Route::get('/test-notification', [NotificationController::class, 'index']);
    Route::post('/send-notification', [NotificationController::class, 'sendToUser']); 

    // Absensi
    Route::get('attendances/log', [AttendanceController::class, 'log'])->name('attendances.log');
    Route::resource('attendances', AttendanceController::class)->only(['index', 'store', 'update']);
    
    // Izin Absensi / Leave Requests
    Route::get('attendance-permit/quota-check', [AttendancePermitController::class, 'quotaCheck'])->name('attendance-permit.quota-check');
    Route::resource('attendance-permit', AttendancePermitController::class)->only(['index', 'show', 'create', 'store', 'update', 'destroy']);

});

Route::middleware(['auth', CheckRole::class.':admin,hr'])->group(function () {
    require __DIR__.'/admin.php';
});

require __DIR__.'/auth.php';

Auth::routes(); 
