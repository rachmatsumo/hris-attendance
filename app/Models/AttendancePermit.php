<?php
// app/Models/AttendancePermit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
class AttendancePermit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'total_day',
        'type', 
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes'
    ];

    protected $casts = [
        // 'date' => 'date', 
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
            'urgent_leave' => 'Izin Mendadak',
            'leave' => 'Cuti'
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute()
    {
        $status = [
            'pending' => 'Tertunda',
            'rejected' => 'Ditolak',
            'approved' => 'Disetujui', 
        ];
        
        return $status[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            'rejected' => 'danger',
            'approved' => 'success',
            'pending'  => 'warning',
            'withdraw'  => 'secondary',
        ];

        $color = $map[$this->status] ?? 'secondary';

        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    public function getPeriodeLocaleAttribute()
    { 
        \Carbon\Carbon::setLocale('id');

        $start = \Carbon\Carbon::parse($this->start_date);
        $end   = \Carbon\Carbon::parse($this->end_date);

        // Format default (tanpa tahun)
        $startFmt = $start->translatedFormat('d F');
        $endFmt   = $end->translatedFormat('d F');

        // Kalau satu hari
        if ($start->equalTo($end)) {
            return $start->translatedFormat('d F Y');
        }

        // Kalau beda tahun
        if ($start->year !== $end->year) {
            $startFmt = $start->translatedFormat('d F Y');
            $endFmt   = $end->translatedFormat('d F Y');
        } else {
            // Tambah tahun hanya di akhir rentang
            $endFmt .= ' ' . $end->year;
        }

        return $startFmt . ' - ' . $endFmt;
    }


    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}