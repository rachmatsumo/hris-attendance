<?php
// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_working_days',
        'total_present_days',
        'total_late_days', 
        'total_absent_days',
        'total_working_hours',
        'total_overtime_hours',
        'basic_salary',
        'meal_allowance_total',
        'overtime_pay',
        'bonus',
        'deductions',
        'gross_salary',
        'net_salary',
        'notes',
        'status',
        'finalized_at',
        'paid_at'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'meal_allowance_total' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'total_working_hours' => 'decimal:2',
        'total_overtime_hours' => 'decimal:2',
        'finalized_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    public function calculateSalary()
    {
        // Basic calculation based on attendance
        $this->basic_salary = $this->total_present_days * $this->user->salary_per_day;
        $this->meal_allowance_total = $this->total_present_days * $this->user->meal_allowance;
        
        // Overtime calculation (1.5x hourly rate)
        $hourlyRate = $this->user->salary_per_day / 8; // Assuming 8 hours per day
        $this->overtime_pay = $this->total_overtime_hours * ($hourlyRate * 1.5);
        
        // Gross salary
        $this->gross_salary = $this->basic_salary + $this->meal_allowance_total + $this->overtime_pay + $this->bonus;
        
        // Net salary (after deductions)
        $this->net_salary = $this->gross_salary - $this->deductions;
        
        $this->save();
    }
}
