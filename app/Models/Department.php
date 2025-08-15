<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'code',
        'address',
        'location_lat',
        'location_lng',
        'radius',
        'is_active'
    ];

    protected $casts = [
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        // 'is_active' => 'boolean',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}