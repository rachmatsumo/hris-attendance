<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['department_id'];

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'position_id', 
        'phone',
        'join_date',
        'profile_photo',
        'is_active',
        'gender',
    ];

    public function scopeExcludeRoles(Builder $query, array $roles)
    {
        return $query->whereNotIn('role', $roles);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [  
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

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
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

    public function leaveQuota($year = null)
    {
        $year = $year ?? date('Y');

        $limit_cuti = Setting::where('key', 'annual_leave_quota')->value('value') ?? 12;
        $limit_izin = Setting::where('key', 'permit_quota')->value('value') ?? 12;

        $count_cuti = $this->attendancePermits()
            ->where('type', 'leave')
            ->whereNotIn('status', ['rejected', 'withdraw'])
            ->whereYear('start_date', $year)
            ->sum('total_day');

        $count_izin = $this->attendancePermits()
            ->where('type', '!=', 'leave')
            ->whereNotIn('status', ['rejected', 'withdraw'])
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

    public function getGenderLocaleAttribute()
    {
        return $this->gender == 'male' ? 'Laki-Laki' : 'Perempuan';
    }

    public function getStatusNameAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function getDepartmentIdAttribute()
    {
        return $this->position?->department_id;
    }

    public function fcmTokens()
    {
        return $this->hasMany(UserFcmToken::class);
    }
}