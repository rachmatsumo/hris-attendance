<?php
// app/Models/LeaveRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    // public function scopePending($query)
    // {
    //     return $query->where('status', 'pending');
    // }

    // public function scopeApproved($query)
    // {
    //     return $query->where('status', 'approved');
    // }

    // public function scopeRejected($query)
    // {
    //     return $query->where('status', 'rejected');
    // }

    // // Accessors
    // public function getAttachmentUrlAttribute()
    // {
    //     return $this->attachment ? asset('storage/' . $this->attachment) : null;
    // }

    // public function getTypeNameAttribute()
    // {
    //     $types = [
    //         'annual_leave' => 'Cuti Tahunan',
    //         'sick' => 'Sakit',
    //         'permission' => 'Izin',
    //         'maternity' => 'Melahirkan',
    //         'emergency' => 'Darurat'
    //     ];
        
    //     return $types[$this->type] ?? $this->type;
    // }

    // public function getStatusNameAttribute()
    // {
    //     $statuses = [
    //         'pending' => 'Menunggu',
    //         'approved' => 'Disetujui', 
    //         'rejected' => 'Ditolak'
    //     ];
        
    //     return $statuses[$this->status] ?? $this->status;
    // }

    // // Methods
    // public function calculateTotalDays()
    // {
    //     $start = Carbon::parse($this->start_date);
    //     $end = Carbon::parse($this->end_date);
        
    //     $totalDays = 0;
    //     $current = $start->copy();
        
    //     while ($current->lte($end)) {
    //         // Skip weekends (Saturday = 6, Sunday = 0)
    //         if (!in_array($current->dayOfWeek, [0, 6])) {
    //             // Check if it's not a holiday
    //             $isHoliday = Holiday::where('date', $current->format('Y-m-d'))
    //                 ->where('is_active', true)
    //                 ->exists();
                    
    //             if (!$isHoliday) {
    //                 $totalDays++;
    //             }
    //         }
            
    //         $current->addDay();
    //     }
        
    //     return $totalDays;
    // }

    // protected static function boot()
    // {
    //     parent::boot();
        
    //     static::creating(function ($leaveRequest) {
    //         $leaveRequest->total_days = $leaveRequest->calculateTotalDays();
    //     });
        
    //     static::updating(function ($leaveRequest) {
    //         if ($leaveRequest->isDirty(['start_date', 'end_date'])) {
    //             $leaveRequest->total_days = $leaveRequest->calculateTotalDays();
    //         }
    //     });
    // }
}