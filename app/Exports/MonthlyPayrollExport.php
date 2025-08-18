<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MonthlyPayrollExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithEvents
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month; // format Y-m, misal 2025-08
    }

    public function collection()
    {
        $year  = date('Y', strtotime($this->month));
        $month = date('m', strtotime($this->month));

        return Payroll::with(['user.position', 'user.department'])
            ->where('year', $year)
            ->where('month', $month)
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Karyawan',
            'Nama Karyawan',
            'Jabatan',
            'Divisi',
            'Gross',
            'Deduction',
            'THP',
            'Jenis Payroll',
            'Status',
        ];
    }

    public function map($payroll): array
    {
        return [
            $payroll->id,
            $payroll->user?->employee_id,
            $payroll->user?->name,
            $payroll->user?->position?->name,
            $payroll->user?->department?->name,
            $payroll->incomes_total,
            $payroll->deductions_total,
            $payroll->net_salary,
            ucfirst($payroll->payroll_type),
            ucfirst($payroll->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Baris 1 = Periode
                $sheet->setCellValue('A1', 'Periode: ' . date('F Y', strtotime($this->month)));
                // Baris 2 = Waktu Download
                $sheet->setCellValue('A2', 'Waktu Download: ' . now()->format('d-m-Y H:i:s'));
                // Baris 3 kosong
                $sheet->setCellValue('A3', '');
            },
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Tentukan range data: dari A4 sampai kolom terakhir & baris terakhir
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $range = "A4:{$highestColumn}{$highestRow}";

                // Border di seluruh tabel
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
    // Header tabel mulai dari baris 4
    public function startCell(): string
    {
        return 'A4';
    }
}
