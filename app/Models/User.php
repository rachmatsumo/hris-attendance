<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'phone',
        'join_date',
        'salary_per_day',
        'meal_allowance',
        'profile_photo',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary_per_day' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendancePermits()
    {
        return $this->hasMany(AttendancePermit::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEmployee($query)
    {
        return $query->where('role', 'employee');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeHR($query)
    {
        return $query->where('role', 'hr');
    }

    // Accessors
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->employee_id . ')';
    }

    // Methods
    public function getTodayAttendance()
    {
        return $this->attendances()->forDate(today())->first();
    }

    public function hasWorkSchedule($dayOfWeek = null)
    {
        $day = $dayOfWeek ?? today()->dayOfWeek;
        return $this->workSchedules()->active()->forDay($day)->exists();
    }

    public function getWorkSchedule($dayOfWeek = null)
    {
        $day = $dayOfWeek ?? today()->dayOfWeek;
        return $this->workSchedules()->active()->forDay($day)->first();
    }

    public function canApprove()
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    public function getMonthlyAttendanceSummary($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $attendances = $this->attendances()->forMonth($month, $year)->get();

        return [
            'total_present' => $attendances->where('status', 'present')->count(),
            'total_late' => $attendances->where('status', 'late')->count(),
            'total_absent' => $attendances->where('status', 'absent')->count(),
            'total_sick' => $attendances->where('status', 'sick')->count(),
            'total_permission' => $attendances->where('status', 'permission')->count(),
            'total_working_hours' => $attendances->sum('working_hours'),
            'total_overtime_hours' => $attendances->sum('overtime_hours'),
        ];
    }
}