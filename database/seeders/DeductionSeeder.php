<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deductions = [
            [
                'name' => 'PPh',
                'level_id' => 1,  
                'value' => 15,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 2,  
                'value' => 15,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 3,  
                'value' => 15,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 4,  
                'value' => 5,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 5,  
                'value' => 5,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 6,  
                'value' => 5,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 7,  
                'value' => 5,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
            [
                'name' => 'PPh',
                'level_id' => 8,  
                'value' => 5,
                'type_value' => 'percent',
                'is_active' => 1,
            ], 
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }

        for ($level = 1; $level <= 8; $level++) {
            Deduction::create([
                'name' => 'BPJS',
                'level_id' => $level,
                'value' => 200000,
                'type_value' => 'fixed',
                'is_active' => 1,
            ]);
        }
    }
}
