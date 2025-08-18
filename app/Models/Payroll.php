<?php
// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';
    
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
        'incomes_data',
        'incomes_total',
        'deductions_data',
        'deductions_total', 
        'net_salary',
        'notes',
        'status',
        'finalized_at',
        'paid_at'
    ];

    // protected $casts = [
    //     'basic_salary' => 'decimal:2',
    //     'meal_allowance_total' => 'decimal:2',
    //     'overtime_pay' => 'decimal:2',
    //     'bonus' => 'decimal:2',
    //     'deductions' => 'decimal:2',
    //     'gross_salary' => 'decimal:2',
    //     'net_salary' => 'decimal:2',
    //     'total_working_hours' => 'decimal:2',
    //     'total_overtime_hours' => 'decimal:2',
    //     'finalized_at' => 'datetime',
    //     'paid_at' => 'datetime',
    // ];

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

    public function getMonthNameAttribute()
    {
        return Carbon::create()->month($this->month)->locale('id')->translatedFormat('F');
    }

    public function getPeriodAttribute()
    {
        return Carbon::create()->month($this->month)->year($this->year)
            ->locale('id')
            ->translatedFormat('F Y');
    }

    public function getIncomesTotalFormattedAttribute()
    {
        return number_format($this->incomes_total, 0, ',', '.');
    }

    public function getDeductionsTotalFormattedAttribute()
    {
        return number_format($this->deductions_total, 0, ',', '.');
    }

    public function getNetSalaryFormattedAttribute()
    {
        return number_format($this->net_salary, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            'paid' => 'success',
            'draft' => 'warning', 
        ];

        $color = $map[$this->status] ?? 'secondary';

        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    public static function generateRegularPayroll(User $user, $month, $year)
    {
        $levelId = $user->position?->level_id;

        // Ambil cut-off dari setting
        $cutOff = setting('attendance_recap_cut_off', 20);

        // Tentukan periode mulai & selesai cut-off
        
        $cutOff = (int) setting('attendance_recap_cut_off', 20);

        // Start: tanggal cut-off+1 bulan sebelumnya
        $startDate = Carbon::create($year, $month, 1)->subMonth()->setDay($cutOff + 1)->startOfDay();

        // End: tanggal cut-off bulan saat ini
        $endDate = Carbon::create($year, $month, 1)->setDay($cutOff)->endOfDay();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalWorkingDays  = $attendances->count();
        $totalPresentDays  = $attendances->where('status', 'present')->count();
        $totalLateDays     = $attendances->where('status', 'late')->count();
        $totalAbsentDays   = $attendances->where('status', 'absent')->count();
        $totalWorkingHours = $attendances->sum('working_hours');

        // Incomes
        $incomes = Income::where('level_id', $levelId)
            ->where('is_active', 1)
            ->get();

        $incomesData  = [];
        $incomesTotal = 0;

        foreach ($incomes as $inc) {
            if ($inc->category === 'base') {
                $val = $inc->value;
            } elseif ($inc->category === 'daily') {
                $val = $inc->value * $totalPresentDays; // hitung berdasarkan cut-off
            } else {
                $val = 0;
            }

            $incomesData[] = [
                'name'  => $inc->name,
                'value' => $val,
            ];
            $incomesTotal += $val;
        }

        // Overtime
        $overtimeSalary = $attendances->sum('overtime_salary');
        if ($overtimeSalary > 0) {
            $incomesData[] = [
                'name'  => 'Overtime',
                'value' => $overtimeSalary,
            ];
            $incomesTotal += $overtimeSalary;
        }

        // Deductions
        $deductions = Deduction::where('level_id', $levelId)
                        ->where('is_active', 1)
                        ->get();
        $deductionsData  = [];
        $deductionsTotal = 0;

        foreach ($deductions as $deduction) {
            if ($deduction->type_value === 'percent') {
                $val = ($incomesTotal * $deduction->value) / 100;
            } else { // fixed
                $val = $deduction->value;
            }

            $deductionsData[] = [
                'name'  => $deduction->name,
                'value' => $val,
            ];
            $deductionsTotal += $val;
        }

        // Net salary
        $netSalary = $incomesTotal - $deductionsTotal;

        $payroll = Payroll::updateOrCreate(
            [
                'user_id' => $user->id,
                'month'   => $month,
                'year'    => $year,
            ],
            [
                'total_working_days' => $totalWorkingDays,
                'total_present_days' => $totalPresentDays,
                'total_late_days'    => $totalLateDays,
                'total_absent_days'  => $totalAbsentDays,
                'total_working_hours'=> $totalWorkingHours,
                'incomes_data'       => json_encode($incomesData),
                'incomes_total'      => $incomesTotal,
                'deductions_data'    => json_encode($deductionsData),
                'deductions_total'   => $deductionsTotal,
                'net_salary'         => $netSalary,
                'notes'              => null,
                'payroll_type'       => 'regular',
                'status'             => 'draft',
                'finalized_at'       => null,
                'paid_at'            => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );

        return $payroll
            ? ['success' => true, 'message' => 'Payroll berhasil disimpan', 'data' => $payroll]
            : ['success' => false, 'message' => 'Gagal menyimpan payroll'];
    }


    // public function calculateSalary()
    // {
    //     // Basic calculation based on attendance
    //     $this->basic_salary = $this->total_present_days * $this->user->salary_per_day;
    //     $this->meal_allowance_total = $this->total_present_days * $this->user->meal_allowance;
        
    //     // Overtime calculation (1.5x hourly rate)
    //     $hourlyRate = $this->user->salary_per_day / 8; // Assuming 8 hours per day
    //     $this->overtime_pay = $this->total_overtime_hours * ($hourlyRate * 1.5);
        
    //     // Gross salary
    //     $this->gross_salary = $this->basic_salary + $this->meal_allowance_total + $this->overtime_pay + $this->bonus;
        
    //     // Net salary (after deductions)
    //     $this->net_salary = $this->gross_salary - $this->deductions;
        
    //     $this->save();
    // }
}
