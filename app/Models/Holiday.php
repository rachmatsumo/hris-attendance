<?php
// app/Models/Holiday.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeNational($query)
    {
        return $query->where('type', 'national');
    }

    public function scopeCompany($query)
    {
        return $query->where('type', 'company');
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'national' => 'Nasional',
            'company' => 'Perusahaan',
            'religious' => 'Keagamaan'
        ];
        
        return $types[$this->type] ?? $this->type;
    }
}