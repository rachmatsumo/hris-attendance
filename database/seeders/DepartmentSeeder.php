<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'DHR',
                // 'address' => 'Gedung Utama Lantai 2',
                // 'location_lat' => -6.200000, // Contoh koordinat Jakarta
                // 'location_lng' => 106.816666,
                // 'radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'DIT', 
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'DFA', 
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'DMA', 
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'DOP', 
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
