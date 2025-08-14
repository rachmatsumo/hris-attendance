<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salary;
use Carbon\Carbon;

class SalariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salaries = [
                        [
                            'position_id'        => 1,
                            'basic_salary'       => 8000000,
                            'transport_allowance'=> 1000000,
                            'meal_allowance'     => 750000,
                            'daily_salary'       => 300000,
                            'overtime_rate'      => 50000,
                            'bonus'              => 1000000,
                            'deductions'         => 500000,
                            'net_salary'         => 10250000, 
                            'effective_date'     => Carbon::parse('2025-01-01'),
                        ],
                        [
                            'position_id'        => 2,
                            'basic_salary'       => 6000000,
                            'transport_allowance'=> 750000,
                            'meal_allowance'     => 500000,
                            'daily_salary'       => 250000,
                            'overtime_rate'      => 40000,
                            'bonus'              => 750000,
                            'deductions'         => 300000,
                            'net_salary'         => 7700000, 
                            'effective_date'     => Carbon::parse('2025-01-01'),
                        ],
                        [
                            'position_id'        => 3,
                            'basic_salary'       => 5000000,
                            'transport_allowance'=> 650000,
                            'meal_allowance'     => 400000,
                            'daily_salary'       => 150000,
                            'overtime_rate'      => 30000,
                            'bonus'              => 550000,
                            'deductions'         => 300000,
                            'net_salary'         => 6300000, 
                            'effective_date'     => Carbon::parse('2025-01-01'),
                        ],
                        [
                            'position_id'        => 4,
                            'basic_salary'       => 5000000,
                            'transport_allowance'=> 650000,
                            'meal_allowance'     => 400000,
                            'daily_salary'       => 150000,
                            'overtime_rate'      => 30000,
                            'bonus'              => 550000,
                            'deductions'         => 300000,
                            'net_salary'         => 6300000, 
                            'effective_date'     => Carbon::parse('2025-01-01'),
                        ],
                        [
                            'position_id'        => 5,
                            'basic_salary'       => 5000000,
                            'transport_allowance'=> 650000,
                            'meal_allowance'     => 400000,
                            'daily_salary'       => 150000,
                            'overtime_rate'      => 30000,
                            'bonus'              => 550000,
                            'deductions'         => 300000,
                            'net_salary'         => 6300000, 
                            'effective_date'     => Carbon::parse('2025-01-01'),
                        ],
                    ];

        foreach ($salaries as $salary) {
            Salary::create($salary);
        }
    }
}
