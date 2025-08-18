<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AttendancePermitAdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\PayrollAdminController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RecapAttendanceController;  
use App\Http\Controllers\Admin\SettingController;  
use App\Http\Controllers\Admin\LocationController;  
use App\Http\Controllers\Admin\WorkingTimeController;  
use App\Http\Controllers\Admin\WorkScheduleController;  
use App\Http\Controllers\Admin\LevelController;  
use App\Http\Controllers\Admin\IncomeController;  
use App\Http\Controllers\Admin\DeductionController;  

use App\Models\User;
use App\Models\WorkSchedule; 

// Route::middleware(['auth', CheckRole::class.':admin,hr'])->group(function () {

Route::get('/admin/menu', [AdminController::class, 'index'])->name('admin.index');

// MASTER DATA ROUTES

Route::resource('department', DepartmentController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('position', PositionController::class)->only('index', 'store', 'show', 'update', 'destroy');

Route::get('user/position/{id}', [UserController::class, 'loadPosition'])->name('user.position');
Route::resource('user', UserController::class)->only('index', 'store', 'show', 'update', 'destroy');

Route::resource('holiday', HolidayController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('setting', SettingController::class)->only('index', 'store', 'show', 'update', 'destroy');
Route::resource('location', LocationController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::resource('working-time', WorkingTimeController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

// RESOURCE MANAGEMENT ROUTES
Route::get('recap-attendance/export/daily', [RecapAttendanceController::class, 'exportDaily'])->name('recap-attendance.export.daily');
Route::get('recap-attendance/export/monthly', [RecapAttendanceController::class, 'exportMonthly'])->name('recap-attendance.export.monthly');
Route::resource('recap-attendance', RecapAttendanceController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

Route::post('payroll-admin/set-paid', [PayrollAdminController::class, 'setPaid'])->name('payroll-admin.set-paid'); 
Route::get('payroll-admin/{id}/download-pdf', [PayrollAdminController::class, 'downloadPdf'])->name('payroll-admin.download-pdf'); 
Route::get('payroll-admin/export', [PayrollAdminController::class, 'exportMonthly'])->name('payroll-admin.export');
Route::resource('payroll-admin', PayrollAdminController::class)->only('index', 'store', 'show', 'destroy'); 

Route::get('work-schedule/{id}/work-schedule.batch-edit', [WorkScheduleController::class, 'batchEdit'])->name('work-schedule.batch-edit'); 
Route::get('work-schedule/batch-create', [WorkScheduleController::class, 'batchCreate'])->name('work-schedule.batch-create');
Route::post('work-schedule/batch-store', [WorkScheduleController::class, 'batchStore'])->name('work-schedule.batch-store'); 
Route::put('work-schedule/{bulk_id}/batch-update', [WorkScheduleController::class, 'batchUpdate'])->name('work-schedule.batch-update'); 
Route::delete('work-schedule/batch/{bulk_id}', [WorkScheduleController::class, 'destroyBulk'])->name('work-schedule.batch-destroy');
Route::get('work-schedule/export', [WorkScheduleController::class, 'exportMonthly'])->name('work-schedule.export');
Route::resource('work-schedule', WorkScheduleController::class)->only('index', 'show');   

Route::resource('attendance-permit-admin', AttendancePermitAdminController::class)->only('index', 'show', 'update', 'destroy'); 

Route::resource('level', LevelController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
Route::prefix('level/{level}')->group(function(){
    Route::get('incomes/modal', [IncomeController::class,'modal'])->name('level.incomes.modal');
    Route::post('incomes/store', [IncomeController::class,'store'])->name('level.incomes.store');

    Route::get('deductions/modal', [DeductionController::class,'modal'])->name('level.deductions.modal');
    Route::post('deductions/store', [DeductionController::class,'store'])->name('level.deductions.store');
});

// });

// Route::resource('income', IncomeController::class)->only('index', 'store', 'show', 'update', 'destroy'); 
// Route::resource('deduction', DeductionController::class)->only('index', 'store', 'show', 'update', 'destroy'); 

  