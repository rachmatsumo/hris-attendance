<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WorkingTime extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'late_tolerance_minutes',
        'end_next_day',
        'code',
        'color',
        'is_active',
    ];
    
    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class, 'working_time_id');
    }
    protected $casts = [
        // 'weekly_schedule' => 'array',
        // 'is_active' => 'boolean'
        'work_date' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function getStartTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    public function getEndTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    public function getScheduleAttribute()
    {
        // Format jam:menit dari start_time dan end_time
        $start = \Carbon\Carbon::parse($this->start_time)->format('H:i');
        $end = \Carbon\Carbon::parse($this->end_time)->format('H:i');

        return "{$start} - {$end}";
    }
}
