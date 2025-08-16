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
use App\Http\Controllers\Admin\WorkingTimeController;  
use App\Http\Controllers\Admin\WorkScheduleController;  

use App\Models\User;
use App\Models\WorkSchedule; 

Route::get('/admin/menu', [AdminController::class, 'index'])->name('admin.index');

// MASTER DATA ROUTES

Route::resource('department', DepartmentController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('position', PositionController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('user', UserController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('holiday', HolidayController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('setting', SettingController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('location', LocationController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::resource('working-time', WorkingTimeController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

// RESOURCE MANAGEMENT ROUTES
Route::resource('recap-attendance', RecapAttendanceController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::resource('payroll', PayrollController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

Route::get('work-schedule/{id}/work-schedule.batch-edit', [WorkScheduleController::class, 'batchEdit'])->name('work-schedule.batch-edit'); 
Route::get('work-schedule/batch-create', [WorkScheduleController::class, 'batchCreate'])->name('work-schedule.batch-create');
Route::post('work-schedule/batch-store', [WorkScheduleController::class, 'batchStore'])->name('work-schedule.batch-store'); 
Route::put('work-schedule/{bulk_id}/batch-update', [WorkScheduleController::class, 'batchUpdate'])->name('work-schedule.batch-update'); 
Route::delete('work-schedule/batch/{bulk_id}', [WorkScheduleController::class, 'destroyBulk'])->name('work-schedule.batch-destroy');
Route::get('work-schedule/export', [WorkScheduleController::class, 'exportMonthly'])->name('work-schedule.export');
Route::resource('work-schedule', WorkScheduleController::class)->only('index', 'show');   


  