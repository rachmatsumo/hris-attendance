<?php
// app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'clock_in_lat',
        'clock_in_lng',
        'clock_out_lat',
        'clock_out_lng',
        'clock_in_photo',
        'clock_out_photo',
        'clock_in_notes',
        'clock_out_notes',
        'status',
        'late_minutes',
        'working_hours',
        'overtime_hours',
        'admin_notes'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'clock_in_lat' => 'decimal:8',
        'clock_in_lng' => 'decimal:8',
        'clock_out_lat' => 'decimal:8',
        'clock_out_lng' => 'decimal:8',
        'working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'user_id', 'user_id')
            ->where('day_of_week', Carbon::parse($this->date)->dayOfWeek);
    }

    // public function getTodayWorkScheduleAttribute()
    // {
    //     return $this->workSchedule()
    //         ->where('day_of_week', Carbon::parse($this->date)->dayOfWeek)
    //         ->first();
    // }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    // Mutators & Accessors
    public function getIsLateAttribute()
    {
        return $this->status === 'late';
    }

    public function getIsPresentAttribute()
    {
        return in_array($this->status, ['present', 'late']);
    }

    public function getClockInPhotoUrlAttribute()
    {
        return $this->clock_in_photo ? asset('storage/' . $this->clock_in_photo) : null;
    }

    public function getClockOutPhotoUrlAttribute()
    {
        return $this->clock_out_photo ? asset('storage/' . $this->clock_out_photo) : null;
    }

    // Methods
    public function calculateWorkingHours()
    {
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return 0;
        }

        $clockIn = Carbon::parse($this->clock_in_time);
        $clockOut = Carbon::parse($this->clock_out_time);
        
        // Get work schedule for the day
        $schedule = $this->user->workSchedules()
            ->where('day_of_week', $this->date->dayOfWeek)
            ->first();
            
        if (!$schedule) {
            return $clockIn->diffInHours($clockOut);
        }

        $totalMinutes = $clockIn->diffInMinutes($clockOut);
        
        // Subtract break time if applicable
        if ($schedule->break_start_time && $schedule->break_end_time) {
            $breakStart = Carbon::parse($schedule->break_start_time);
            $breakEnd = Carbon::parse($schedule->break_end_time);
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
            
            // Only subtract if work time spans over break time
            if ($clockIn->lt($breakEnd) && $clockOut->gt($breakStart)) {
                $totalMinutes -= $breakMinutes;
            }
        }
        
        return round($totalMinutes / 60, 2);
    }

    public function calculateOvertimeHours()
    {
        $schedule = $this->user->workSchedules()
            ->where('day_of_week', $this->date->dayOfWeek)
            ->first();
            
        if (!$schedule || !$this->clock_out_time) {
            return 0;
        }

        $scheduledEnd = Carbon::parse($this->date->format('Y-m-d') . ' ' . $schedule->end_time);
        $actualEnd = Carbon::parse($this->clock_out_time);
        
        if ($actualEnd->gt($scheduledEnd)) {
            return round($scheduledEnd->diffInMinutes($actualEnd) / 60, 2);
        }
        
        return 0;
    }

    public function updateWorkingHours()
    {
        $this->working_hours = $this->calculateWorkingHours();
        $this->overtime_hours = $this->calculateOvertimeHours();
        $this->save();
    }
}
