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
                'code' => 'HR',
                'address' => 'Gedung Utama Lantai 2',
                'location_lat' => -6.200000, // Contoh koordinat Jakarta
                'location_lng' => 106.816666,
                'radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'address' => 'Gedung Utama Lantai 3',
                'location_lat' => -6.200000,
                'location_lng' => 106.816666,
                'radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'FA',
                'address' => 'Gedung Utama Lantai 1',
                'location_lat' => -6.200000,
                'location_lng' => 106.816666,
                'radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'address' => 'Gedung Utama Lantai 4',
                'location_lat' => -6.200000,
                'location_lng' => 106.816666,
                'radius' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'address' => 'Gedung Produksi',
                'location_lat' => -6.201000,
                'location_lng' => 106.817000,
                'radius' => 200,
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
