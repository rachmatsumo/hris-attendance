<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deduction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'level_id',
        'value',
        'type_value',
        'is_active',
    ];

    public function getValueAttribute($value)
    {
        return $this->type_value === 'fixed' ? (int) $value : $value;
    }
}
