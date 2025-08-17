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
        $departments = range(1, 5);  
        $positionsTemplate = [
            ['name' => 'Manager', 'level_id' => 3],
            ['name' => 'Assistant Manager', 'level_id' => 4],
            ['name' => 'Supervisor', 'level_id' => 5],
            ['name' => 'Staff', 'level_id' => 6],
            ['name' => 'Magang', 'level_id' => 8],
        ];

        foreach ($departments as $deptId) {
            foreach ($positionsTemplate as $pos) {
                Position::create([
                    'name' => $pos['name'],
                    'department_id' => $deptId,
                    'level_id' => $pos['level_id'],
                ]);
            }
        }
    }
}
