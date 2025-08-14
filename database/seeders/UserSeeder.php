<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'employee_id' => 'ADM001',
            'name' => 'Super Admin',
            'email' => 'admin@company.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department_id' => 1,
            'phone' => '08123456789',
            'join_date' => '2024-01-01',
            'salary_per_day' => 0,
            'meal_allowance' => 0,
            'is_active' => true,
        ]);

        // HR Manager
        User::create([
            'employee_id' => 'HR001',
            'name' => 'HR Manager',
            'email' => 'hr@company.com',
            'password' => Hash::make('password123'),
            'role' => 'hr',
            'department_id' => 1,
            'phone' => '08123456790',
            'join_date' => '2024-01-01',
            'salary_per_day' => 500000,
            'meal_allowance' => 25000,
            'is_active' => true,
        ]);

        // Sample Employees
        $employees = [
            [
                'employee_id' => 'IT001',
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'department_id' => 2,
                'salary_per_day' => 400000,
                'meal_allowance' => 20000,
            ],
            [
                'employee_id' => 'IT002',
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'department_id' => 2,
                'salary_per_day' => 450000,
                'meal_allowance' => 20000,
            ],
            [
                'employee_id' => 'FA001',
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@company.com',
                'department_id' => 3,
                'salary_per_day' => 380000,
                'meal_allowance' => 18000,
            ],
            [
                'employee_id' => 'MKT001',
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@company.com',
                'department_id' => 4,
                'salary_per_day' => 350000,
                'meal_allowance' => 17000,
            ],
            [
                'employee_id' => 'OPS001',
                'name' => 'David Brown',
                'email' => 'david.brown@company.com',
                'department_id' => 5,
                'salary_per_day' => 300000,
                'meal_allowance' => 15000,
            ],
        ];

        foreach ($employees as $employee) {
            User::create(array_merge($employee, [
                'password' => Hash::make('password123'),
                'role' => 'employee',
                'phone' => '0812345679' . rand(10, 99),
                'join_date' => '2024-01-15',
                'is_active' => true,
            ]));
        }
    }
}
