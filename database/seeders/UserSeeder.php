<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $counter = 1065601; // mulai employee_id

        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@hris.com'],
            [
                'employee_id' => $counter++,
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'position_id' => 1,
                'gender' => 'male',
                'phone' => '08123456789',
                'join_date' => '2024-01-01', 
                'is_active' => true,
            ]
        );

        // HR Manager
        User::updateOrCreate(
            ['email' => 'hr@hris.com'],
            [
                'employee_id' => $counter++,
                'name' => 'HR Manager',
                'password' => Hash::make('password123'),
                'role' => 'hr',
                'position_id' => 1,
                'gender' => 'female',
                'phone' => '08123456790',
                'join_date' => '2024-01-01', 
                'is_active' => true,
            ]
        );

        $departments = [
            1 => 'Human Resources',
            2 => 'Information Technology',
            3 => 'Finance & Accounting',
            4 => 'Marketing',
            5 => 'Operations',
        ];

        $positionsTemplate = [
            ['name' => 'Manager', 'level_id' => 3, 'count' => 1],
            ['name' => 'Assistant Manager', 'level_id' => 4, 'count' => 1],
            ['name' => 'Supervisor', 'level_id' => 5, 'count' => 1],
            ['name' => 'Staff', 'level_id' => 6, 'count' => 2],
            ['name' => 'Magang', 'level_id' => 8, 'count' => 1],
        ];

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Linda', 'James', 'Jessica'];
        $lastNames  = ['Doe', 'Smith', 'Johnson', 'Wilson', 'Brown', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White'];

        $emailsUsed = [];
        $phonesUsed = [];

        foreach ($departments as $deptId => $deptName) {
            foreach ($positionsTemplate as $position) {
                for ($i = 1; $i <= $position['count']; $i++) {
                    // Ambil nama random
                    $firstName = $firstNames[array_rand($firstNames)];
                    $lastName  = $lastNames[array_rand($lastNames)];
                    $fullName  = "$firstName $lastName";

                    // Generate email unik
                    do {
                        $email = strtolower(str_replace(' ', '.', $fullName)) . rand(1, 99) . '@hris.com';
                    } while (in_array($email, $emailsUsed));
                    $emailsUsed[] = $email;

                    // Generate phone unik
                    do {
                        $phone = '0812345' . rand(1000, 9999);
                    } while (in_array($phone, $phonesUsed));
                    $phonesUsed[] = $phone;

                    User::updateOrCreate(
                        ['email' => $email],
                        [
                            'employee_id' => $counter++, // incremental
                            'name' => $fullName,
                            'password' => Hash::make('password123'),
                            'role' => 'employee',
                            'position_id' => $position['level_id'],
                            'gender' => rand(0,1) ? 'male' : 'female',
                            'phone' => $phone,
                            'join_date' => now()->subMonths(rand(1,12))->format('Y-m-d'),
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
