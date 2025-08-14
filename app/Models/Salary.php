<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
