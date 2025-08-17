<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Income;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomes = [
            [
                'name' => 'Gaji Dasar',
                'level_id' => 1,  
                'category' => 'base',
                'value' => 13500000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 1,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 2,  
                'category' => 'base',
                'value' => 6900000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 2,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 3,  
                'category' => 'base',
                'value' => 4700000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 3,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 4,  
                'category' => 'base',
                'value' => 3500000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 4,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 5,  
                'category' => 'base',
                'value' => 2700000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 5,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 6,  
                'category' => 'base',
                'value' => 2200000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 6,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 7,  
                'category' => 'base',
                'value' => 1500000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 7,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 

            [
                'name' => 'Gaji Dasar',
                'level_id' => 8,  
                'category' => 'base',
                'value' => 1000000,
                'is_active' => 1,
            ], 
            [
                'name' => 'Uang Makan',
                'level_id' => 8,  
                'category' => 'daily',
                'value' => 50000,
                'is_active' => 1,
            ], 
            
        ];

        foreach ($incomes as $income) {
            Income::create($income);
        }
    }
}
