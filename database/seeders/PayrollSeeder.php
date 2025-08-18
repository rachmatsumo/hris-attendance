<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Income;
use App\Models\Deduction;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $month = date('n');
        $year  = date('Y'); 

        $users = User::with('position')->get();
        
        foreach ($users as $user) {
            Payroll::generateRegularPayroll($user, $month, $year);
        }
    }
}
