<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyRecapAttendanceWithSummaryExport implements WithMultipleSheets
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            new MonthlyRecapAttendanceExport($this->month), // Sheet 1: detail
            new MonthlyRecapAttendanceSummarySheet($this->month),     // Sheet 2: summary
        ];
    }
}
