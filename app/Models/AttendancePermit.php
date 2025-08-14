<?php
// app/Models/AttendancePermit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendancePermit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'requested_time',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes'
    ];

    protected $casts = [
        'date' => 'date',
        'requested_time' => 'datetime:H:i:s',
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
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        $types = [
            'late_arrival' => 'Terlambat Masuk',
            'early_departure' => 'Pulang Cepat',
            'sick_during_work' => 'Sakit Saat Kerja',
            'urgent_leave' => 'Izin Mendadak'
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}