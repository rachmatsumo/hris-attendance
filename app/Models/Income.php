<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'level_id',
        'category',
        'value', 
        'is_active',
    ];

    public function getValueAttribute($value)
    { 
        return (int) $value;
    }
}
