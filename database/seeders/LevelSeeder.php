<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Direktur',
                'grade' => 1,  
            ], 
            [
                'name' => 'Senior Leader',
                'grade' => 2
            ], 
            [
                'name' => 'Middle Leader',
                'grade' => 3
            ], 
            [
                'name' => 'First Leader',
                'grade' => 4
            ], 
            [
                'name' => 'Senior Officer',
                'grade' => 5
            ], 
            [
                'name' => 'Officer',
                'grade' => 6
            ], 
            [
                'name' => 'Junior Officer',
                'grade' => 7
            ], 
            [
                'name' => 'Magang',
                'grade' => 8
            ], 
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
