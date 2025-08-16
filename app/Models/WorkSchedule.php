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

    // public function scopeForDay($query, $dayOfWeek)
    // {
    //     return $query->where('day_of_week', $dayOfWeek);
    // }

    // // Get day name
    // public function getDayNameAttribute()
    // {
    //     $days = [
    //         1 => 'Monday',
    //         2 => 'Tuesday', 
    //         3 => 'Wednesday',
    //         4 => 'Thursday',
    //         5 => 'Friday',
    //         6 => 'Saturday',
    //         7 => 'Sunday'
    //     ];
        
    //     return $days[$this->day_of_week] ?? '';
    // }

    // public function getDayOfWeekNameAttribute($lang)
    // {
    //     $days = [
    //         1 => 'Senin',
    //         2 => 'Selasa',
    //         3 => 'Rabu',
    //         4 => 'Kamis',
    //         5 => 'Jumat',
    //         6 => 'Sabtu',
    //         7 => 'Minggu',
    //     ];

    //     return $days[$this->day_of_week] ?? null;
    // }

    // public function getStartTimeFormattedAttribute()
    // {
    //     return \Carbon\Carbon::parse($this->start_time)->format('H:i');
    // }

    // public function getEndTimeFormattedAttribute()
    // {
    //     return \Carbon\Carbon::parse($this->end_time)->format('H:i');
    // }

    public function workingTime()
    {
        return $this->belongsTo(WorkingTime::class, 'working_time_id');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'work_schedule_id', 'id');
    }

}