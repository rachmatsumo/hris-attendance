<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Manager', 'code' => 'POS001', 'department_id' => 1],
            ['name' => 'Assistant Manager', 'code' => 'POS002', 'department_id' => 2],
            ['name' => 'Supervisor', 'code' => 'POS003', 'department_id' => 3],
            ['name' => 'Staff', 'code' => 'POS004', 'department_id' => 4],
            ['name' => 'Intern', 'code' => 'POS005', 'department_id' => 5],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
