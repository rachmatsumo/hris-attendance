<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $year = date('Y');

        $holidays = [
            [
                'name' => 'Tahun Baru',
                'date' => $year.'-01-01',
                'type' => 'national',
                'description' => 'Hari Tahun Baru Masehi',
            ],
            [
                'name' => 'Imlek',
                'date' => $year.'-02-10',
                'type' => 'national',
                'description' => 'Tahun Baru Imlek',
            ],
            [
                'name' => 'Hari Raya Nyepi',
                'date' => $year.'-03-11',
                'type' => 'national',
                'description' => 'Hari Raya Nyepi (Tahun Baru Saka)',
            ],
            [
                'name' => 'Wafat Isa Almasih',
                'date' => $year.'-03-29',
                'type' => 'national',
                'description' => 'Wafat Isa Almasih',
            ],
            [
                'name' => 'Idul Fitri',
                'date' => $year.'-04-10',
                'type' => 'national',
                'description' => 'Hari Raya Idul Fitri',
            ],
            [
                'name' => 'Idul Fitri',
                'date' => $year.'-04-11',
                'type' => 'national',
                'description' => 'Hari Raya Idul Fitri (Hari ke-2)',
            ],
            [
                'name' => 'Hari Buruh',
                'date' => $year.'-05-01',
                'type' => 'national',
                'description' => 'Hari Buruh Internasional',
            ],
            [
                'name' => 'Kenaikan Isa Almasih',
                'date' => $year.'-05-09',
                'type' => 'national',
                'description' => 'Kenaikan Isa Almasih',
            ],
            [
                'name' => 'Hari Raya Waisak',
                'date' => $year.'-05-23',
                'type' => 'national',
                'description' => 'Hari Raya Waisak',
            ],
            [
                'name' => 'Hari Pancasila',
                'date' => $year.'-06-01',
                'type' => 'national',
                'description' => 'Hari Lahir Pancasila',
            ],
            [
                'name' => 'Idul Adha',
                'date' => $year.'-06-17',
                'type' => 'national',
                'description' => 'Hari Raya Idul Adha',
            ],
            [
                'name' => 'Tahun Baru Islam',
                'date' => $year.'-07-07',
                'type' => 'national',
                'description' => 'Tahun Baru Islam (1 Muharram)',
            ],
            [
                'name' => 'HUT RI ke-79',
                'date' => $year.'-08-17',
                'type' => 'national',
                'description' => 'Hari Kemerdekaan Republik Indonesia',
            ],
            [
                'name' => 'Maulid Nabi Muhammad SAW',
                'date' => $year.'-09-15',
                'type' => 'national',
                'description' => 'Maulid Nabi Muhammad SAW',
            ],
            [
                'name' => 'Hari Natal',
                'date' => $year.'-12-25',
                'type' => 'national',
                'description' => 'Hari Raya Natal',
            ],
            // Company holidays
            [
                'name' => 'Cuti Bersama Lebaran',
                'date' => $year.'-04-08',
                'type' => 'company',
                'description' => 'Cuti Bersama Menjelang Idul Fitri',
            ],
            [
                'name' => 'Cuti Bersama Lebaran',
                'date' => $year.'-04-09',
                'type' => 'company',
                'description' => 'Cuti Bersama Menjelang Idul Fitri',
            ],
            [
                'name' => 'Cuti Bersama Lebaran',
                'date' => $year.'-04-12',
                'type' => 'company',
                'description' => 'Cuti Bersama Setelah Idul Fitri',
            ],
            [
                'name' => 'HUT Perusahaan',
                'date' => $year.'-10-15',
                'type' => 'company',
                'description' => 'Hari Ulang Tahun Perusahaan',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create(array_merge($holiday, ['is_active' => true]));
        }
    }
}
