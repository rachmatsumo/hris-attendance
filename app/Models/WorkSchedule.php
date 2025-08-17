<?php
// app/Models/WorkSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'working_time_id',
        'is_active'
    ];

    // protected $casts = [
    //     'start_time' => 'datetime:H:i:s',
    //     'end_time' => 'datetime:H:i:s',
    //     'break_start_time' => 'datetime:H:i:s',
    //     'break_end_time' => 'datetime:H:i:s',
    //     'is_active' => 'boolean',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    } 

    public function workingTime()
    {
        return $this->belongsTo(WorkingTime::class, 'working_time_id');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'work_schedule_id', 'id');
    }

    public function attendancePermit()
    {
        return $this->hasOne(AttendancePermit::class, 'user_id', 'user_id')
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $this->work_date)
                    ->whereDate('end_date', '>=', $this->work_date);
    }
 
    public function getDisplayCodeAttribute()
    {
        if ($permit = $this->attendancePermit) {
            return $permit->type === 'leave' ? 'Cuti' : 'Izin';
        }

        return $this->workingTime?->code ?? '-';
    }

}