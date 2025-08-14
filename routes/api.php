<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\AttendancePermitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\WorkScheduleController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ================================================================
// PUBLIC ROUTES (No Authentication Required)
// ================================================================

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']); // Optional, jika allow self registration
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// ================================================================
// AUTHENTICATED ROUTES
// ================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // ============================================================
    // AUTH & PROFILE ROUTES
    // ============================================================
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/upload-avatar', [AuthController::class, 'uploadAvatar']);
    });

    // ============================================================
    // DASHBOARD ROUTES
    // ============================================================
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);

    // ============================================================
    // ATTENDANCE ROUTES
    // ============================================================
    Route::prefix('attendance')->group(function () {
        // Clock In/Out
        Route::post('/clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('/clock-out', [AttendanceController::class, 'clockOut']);
        
        // Attendance Info
        Route::get('/today', [AttendanceController::class, 'today']);
        Route::get('/status', [AttendanceController::class, 'status']);
        Route::get('/my-attendances', [AttendanceController::class, 'myAttendances']);
        Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary']);
        
        // Admin/HR Routes
        Route::middleware('role:admin,hr')->group(function () {
            Route::get('/', [AttendanceController::class, 'index']);
            Route::get('/{attendance}', [AttendanceController::class, 'show']);
            Route::put('/{attendance}', [AttendanceController::class, 'update']);
            Route::delete('/{attendance}', [AttendanceController::class, 'destroy']);
            Route::post('/manual-entry', [AttendanceController::class, 'manualEntry']);
            Route::get('/employee/{user}', [AttendanceController::class, 'employeeAttendances']);
        });
    });

    // ============================================================
    // LEAVE REQUEST ROUTES
    // ============================================================
    Route::prefix('leave-requests')->group(function () {
        // Employee Routes
        Route::get('/my-requests', [LeaveRequestController::class, 'myRequests']);
        Route::post('/', [LeaveRequestController::class, 'store']);
        Route::get('/{leaveRequest}', [LeaveRequestController::class, 'show']);
        Route::put('/{leaveRequest}', [LeaveRequestController::class, 'update'])->middleware('can:update,leaveRequest');
        Route::delete('/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->middleware('can:delete,leaveRequest');
        
        // Admin/HR Routes
        Route::middleware('role:admin,hr')->group(function () {
            Route::get('/', [LeaveRequestController::class, 'index']);
            Route::get('/pending', [LeaveRequestController::class, 'pending']);
            Route::post('/{leaveRequest}/approve', [LeaveRequestController::class, 'approve']);
            Route::post('/{leaveRequest}/reject', [LeaveRequestController::class, 'reject']);
        });
        
        // Leave Balance
        Route::get('/balance/my', [LeaveRequestController::class, 'myBalance']);
        Route::get('/balance/{user}', [LeaveRequestController::class, 'userBalance'])->middleware('role:admin,hr');
    });

    // ============================================================
    // ATTENDANCE PERMIT ROUTES
    // ============================================================
    Route::prefix('attendance-permits')->group(function () {
        // Employee Routes
        Route::get('/my-permits', [AttendancePermitController::class, 'myPermits']);
        Route::post('/', [AttendancePermitController::class, 'store']);
        Route::get('/{attendancePermit}', [AttendancePermitController::class, 'show']);
        Route::put('/{attendancePermit}', [AttendancePermitController::class, 'update'])->middleware('can:update,attendancePermit');
        
        // Admin/HR Routes
        Route::middleware('role:admin,hr')->group(function () {
            Route::get('/', [AttendancePermitController::class, 'index']);
            Route::get('/pending', [AttendancePermitController::class, 'pending']);
            Route::post('/{attendancePermit}/approve', [AttendancePermitController::class, 'approve']);
            Route::post('/{attendancePermit}/reject', [AttendancePermitController::class, 'reject']);
        });
    });

    // ============================================================
    // WORK SCHEDULE ROUTES
    // ============================================================
    Route::prefix('work-schedules')->group(function () {
        Route::get('/my-schedule', [WorkScheduleController::class, 'mySchedule']);
        Route::get('/today', [WorkScheduleController::class, 'todaySchedule']);
        
        // Admin/HR Routes
        Route::middleware('role:admin,hr')->group(function () {
            Route::get('/', [WorkScheduleController::class, 'index']);
            Route::post('/', [WorkScheduleController::class, 'store']);
            Route::get('/{workSchedule}', [WorkScheduleController::class, 'show']);
            Route::put('/{workSchedule}', [WorkScheduleController::class, 'update']);
            Route::delete('/{workSchedule}', [WorkScheduleController::class, 'destroy']);
            Route::get('/user/{user}', [WorkScheduleController::class, 'userSchedule']);
            Route::post('/bulk-update', [WorkScheduleController::class, 'bulkUpdate']);
        });
    });

    // ============================================================
    // PAYROLL ROUTES
    // ============================================================
    Route::prefix('payroll')->group(function () {
        // Employee Routes
        Route::get('/my-payslips', [PayrollController::class, 'myPayslips']);
        Route::get('/payslip/{payroll}', [PayrollController::class, 'payslip'])->middleware('can:view,payroll');
        
        // Admin/HR Routes
        Route::middleware('role:admin,hr')->group(function () {
            Route::get('/', [PayrollController::class, 'index']);
            Route::post('/generate', [PayrollController::class, 'generate']);
            Route::get('/{payroll}', [PayrollController::class, 'show']);
            Route::put('/{payroll}', [PayrollController::class, 'update']);
            Route::post('/{payroll}/finalize', [PayrollController::class, 'finalize']);
            Route::post('/{payroll}/mark-paid', [PayrollController::class, 'markPaid']);
            Route::get('/export/{month}/{year}', [PayrollController::class, 'export']);
        });
    });

    // ============================================================
    // REPORT ROUTES
    // ============================================================
    Route::prefix('reports')->middleware('role:admin,hr')->group(function () {
        Route::get('/attendance/monthly', [ReportController::class, 'monthlyAttendance']);
        Route::get('/attendance/daily', [ReportController::class, 'dailyAttendance']);
        Route::get('/attendance/employee/{user}', [ReportController::class, 'employeeAttendance']);
        Route::get('/late-arrivals', [ReportController::class, 'lateArrivals']);
        Route::get('/absences', [ReportController::class, 'absences']);
        Route::get('/overtime', [ReportController::class, 'overtime']);
        Route::get('/leave-summary', [ReportController::class, 'leaveSummary']);
        Route::get('/payroll-summary', [ReportController::class, 'payrollSummary']);
        
        // Export Reports
        Route::get('/export/attendance', [ReportController::class, 'exportAttendance']);
        Route::get('/export/payroll', [ReportController::class, 'exportPayroll']);
    });

    // ============================================================
    // USER MANAGEMENT ROUTES (Admin Only)
    // ============================================================
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::post('/{user}/activate', [UserController::class, 'activate']);
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate']);
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword']);
    });

    // ============================================================
    // DEPARTMENT ROUTES (Admin Only)
    // ============================================================
    Route::prefix('departments')->middleware('role:admin')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{department}', [DepartmentController::class, 'show']);
        Route::put('/{department}', [DepartmentController::class, 'update']);
        Route::delete('/{department}', [DepartmentController::class, 'destroy']);
        Route::get('/{department}/employees', [DepartmentController::class, 'employees']);
    });

    // ============================================================
    // HOLIDAY ROUTES
    // ============================================================
    Route::prefix('holidays')->group(function () {
        Route::get('/', [HolidayController::class, 'index']);
        Route::get('/upcoming', [HolidayController::class, 'upcoming']);
        Route::get('/year/{year}', [HolidayController::class, 'byYear']);
        
        // Admin Only
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [HolidayController::class, 'store']);
            Route::get('/{holiday}', [HolidayController::class, 'show']);
            Route::put('/{holiday}', [HolidayController::class, 'update']);
            Route::delete('/{holiday}', [HolidayController::class, 'destroy']);
        });
    });

    // ============================================================
    // NOTIFICATION ROUTES
    // ============================================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    // ============================================================
    // SETTINGS ROUTES (Admin Only)
    // ============================================================
    Route::prefix('settings')->middleware('role:admin')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::get('/{key}', [SettingController::class, 'show']);
        Route::put('/{key}', [SettingController::class, 'update']);
        Route::post('/bulk-update', [SettingController::class, 'bulkUpdate']);
    });

    // ============================================================
    // UTILITY ROUTES
    // ============================================================
    Route::prefix('utils')->group(function () {
        // Location validation
        Route::post('/validate-location', function (Request $request) {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);
            
            $user = auth()->user();
            $department = $user->department;
            
            if (!$department) {
                return response()->json(['valid' => false, 'message' => 'Department not found']);
            }
            
            $locationService = new \App\Services\LocationService();
            $isValid = $locationService->isWithinRadius(
                $department,
                $request->latitude,
                $request->longitude
            );
            
            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Location valid' : 'You are outside the allowed area',
                'distance' => $locationService->calculateDistance(
                    $department->location_lat,
                    $department->location_lng,
                    $request->latitude,
                    $request->longitude
                )
            ]);
        });
        
        // File upload
        Route::post('/upload', function (Request $request) {
            $request->validate([
                'file' => 'required|file|max:5120', // 5MB max
                'type' => 'required|in:avatar,document,photo'
            ]);
            
            $path = $request->file('file')->store($request->type . 's', 'public');
            
            return response()->json([
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        });
    });
});

// ================================================================
// FALLBACK ROUTES
// ================================================================

// API 404 Handler
Route::fallback(function () {
    return response()->json([
        'error' => 'API endpoint not found',
        'message' => 'The requested API endpoint does not exist.',
        'available_versions' => ['v1']
    ], 404);
});