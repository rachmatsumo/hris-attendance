<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RecapAttendanceController;  
use App\Http\Controllers\Admin\SettingController;  
use App\Http\Controllers\Admin\LocationController;  
use App\Http\Controllers\Admin\WorkScheduleController;  
use Illuminate\Support\Facades\Route;

Route::get('/admin/menu', [AdminController::class, 'index'])->name('admin.index');

// MASTER DATA ROUTES

Route::resource('department', DepartmentController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('position', PositionController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('user', UserController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('holiday', HolidayController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('setting', SettingController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('location', LocationController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

// RESOURCE MANAGEMENT ROUTES
Route::resource('recap-attendance', RecapAttendanceController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::resource('payroll', PayrollController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::resource('work-schedule', WorkScheduleController::class)->only('index', 'store', 'show', 'update', 'destroy');  
  