<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'company_name',
                'value' => 'Rachmatsumo Tech Stark',
                'type' => 'string',
                'description' => 'Nama perusahaan',
            ],
            [
                'key' => 'company_address',
                'value' => 'Tangerang, Indonesia',
                'type' => 'string',
                'description' => 'Alamat perusahaan',
            ],
            [
                'key' => 'work_days_per_week',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Jumlah hari kerja per minggu',
            ],
            [
                'key' => 'work_hours_per_day',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Jumlah jam kerja per hari',
            ],
            [
                'key' => 'overtime_rate',
                'value' => '1.5',
                'type' => 'string',
                'description' => 'Rate lembur (kali dari gaji normal)',
            ],   
            [
                'key' => 'annual_leave_quota',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Kuota cuti tahunan (hari)',
            ],
            [
                'key' => 'sick_leave_quota',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Kuota cuti sakit (hari)',
            ],
            [
                'key' => 'photo_required_clock_in',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Wajib foto saat clock in',
            ],
            [
                'key' => 'photo_required_clock_out',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Wajib foto saat clock out',
            ],
            [
                'key' => 'location_required',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Wajib lokasi saat absen',
            ], 
            [
                'key' => 'attendance_recap_cut_off',
                'value' => '20',
                'type' => 'integer',
                'description' => 'Tanggal cut off payroll setiap bulan',
            ],
            [
                'key' => 'payroll_date',
                'value' => '25',
                'type' => 'integer',
                'description' => 'Tanggal payroll setiap bulan',
            ],
            [
                'key' => 'weekend_overtime_rate',
                'value' => '2.0',
                'type' => 'string',
                'description' => 'Rate lembur akhir pekan',
            ],
            [
                'key' => 'holiday_overtime_rate',
                'value' => '3.0',
                'type' => 'string',
                'description' => 'Rate lembur hari libur',
            ], 
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'description' => 'Timezone aplikasi. WIB : Asia/Jakarta, WITA : Asia/Makassar, WIT : Asia/Jayapura',
            ],  
            [
                'key' => 'min_work_hours_full_pay',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Minimum jam kerja untuk gaji penuh',
            ],
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Aktifkan notifikasi email',
            ],  
            [
                'key' => 'report_email',
                'value' => 'hr@company.com',
                'type' => 'string',
                'description' => 'Email untuk laporan otomatis',
            ]
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
