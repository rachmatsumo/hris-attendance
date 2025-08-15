<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'name',
        'department_id',
        'code',
        'is_active',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function salary()
    {
        return $this->hasOne(Salary::class, 'position_id');
    }

    // public function salary()
    // {
    //     return $this->hasOne(Salary::class);
    // }
}
