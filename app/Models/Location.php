<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'lat_long',
        'is_active',
        'radius',
    ];

      public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getStatusNameAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }
}
