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

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            Position::class,
            'id',            // Foreign key di positions yang menghubungkan ke user
            'id',            // Foreign key di departments yang menghubungkan ke position
            'position_id',   // Local key di users
            'department_id'  // Local key di positions
        );
    }

    // public function department()
    // {
    //     return $this->belongsTo(Department::class);
    // }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // public function leaveRequests()
    // {
    //     return $this->hasMany(LeaveRequest::class);
    // }

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

    public function leaveQuota($year = null)
    {
        $year = $year ?? date('Y');

        $limit_cuti = Setting::where('key', 'annual_leave_quota')->value('value') ?? 12;
        $limit_izin = Setting::where('key', 'permit_quota')->value('value') ?? 12;

        $count_cuti = $this->attendancePermits()
            ->where('type', 'leave')
            ->where('status', '!=', 'rejected')
            ->whereYear('start_date', $year)
            ->sum('total_day');

        $count_izin = $this->attendancePermits()
            ->where('type', '!=', 'leave')
            ->where('status', '!=', 'rejected')
            ->whereYear('start_date', $year)
            ->sum('total_day');

        return [
            'limit_cuti' => $limit_cuti,
            'limit_izin' => $limit_izin,
            'count_cuti' => $count_cuti,
            'count_izin' => $count_izin,
            'sisa_cuti'  => $limit_cuti - $count_cuti,
            'sisa_izin'  => $limit_izin - $count_izin,
        ];
    }


    // public function scopeEmployee($query)
    // {
    //     return $query->where('role', 'employee');
    // }

    // public function scopeAdmin($query)
    // {
    //     return $query->where('role', 'admin');
    // }

    // public function scopeHR($query)
    // {
    //     return $query->where('role', 'hr');
    // }

    // // Accessors
    // public function getProfilePhotoUrlAttribute()
    // {
    //     return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    // }

    // public function getFullNameAttribute()
    // {
    //     return $this->name . ' (' . $this->employee_id . ')';
    // }

    // // Methods
    // public function getTodayAttendance()
    // {
    //     return $this->attendances()->forDate(today())->first();
    // }

    // public function hasWorkSchedule($dayOfWeek = null)
    // {
    //     $day = $dayOfWeek ?? today()->dayOfWeek;
    //     return $this->workSchedules()->active()->forDay($day)->exists();
    // }

    // public function getWorkSchedule($dayOfWeek = null)
    // {
    //     $day = $dayOfWeek ?? today()->dayOfWeek;
    //     return $this->workSchedules()->active()->forDay($day)->first();
    // }

    // public function canApprove()
    // {
    //     return in_array($this->role, ['admin', 'hr']);
    // }

    // public function getMonthlyAttendanceSummary($month = null, $year = null)
    // {
    //     $month = $month ?? now()->month;
    //     $year = $year ?? now()->year;

    //     $attendances = $this->attendances()->forMonth($month, $year)->get();

    //     return [
    //         'total_present' => $attendances->where('status', 'present')->count(),
    //         'total_late' => $attendances->where('status', 'late')->count(),
    //         'total_absent' => $attendances->where('status', 'absent')->count(),
    //         'total_sick' => $attendances->where('status', 'sick')->count(),
    //         'total_permission' => $attendances->where('status', 'permission')->count(),
    //         'total_working_hours' => $attendances->sum('working_hours'),
    //         'total_overtime_hours' => $attendances->sum('overtime_hours'),
    //     ];
    // }
}