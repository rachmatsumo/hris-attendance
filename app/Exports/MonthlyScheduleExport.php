<?php

namespace App\Exports;

use App\Models\WorkSchedule;
use App\Models\User;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyScheduleExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $month;
    protected $users;
    protected $workingTimes;

    public function __construct($month, $users = null)
    {
        $this->month = $month;
        $this->users = $users ?: User::active()->get();
        $this->workingTimes = WorkingTime::all();
    }

    public function collection()
    {
        // Gabungkan users dengan keterangan working times
        $data = collect();
        
        // Tambahkan users
        foreach ($this->users as $user) {
            $data->push($user);
        }
        
        // Tambahkan baris kosong
        $data->push((object)['name' => '', 'is_separator' => true]);
        
        // Tambahkan header bulan
        $monthName = Carbon::createFromFormat('Y-m', $this->month)->locale('id')->format('F Y');
        $data->push((object)['name' => 'Bulan : ' . $monthName, 'is_month_header' => true]);
        
        // Tambahkan baris kosong lagi
        $data->push((object)['name' => '', 'is_separator' => true]);
        
        // Tambahkan keterangan
        $data->push((object)['name' => 'Keterangan:', 'is_legend_header' => true]);
        
        // Tambahkan setiap working time sebagai keterangan
        foreach ($this->workingTimes as $workingTime) {
            $data->push((object)[
                'name' => $workingTime->code . ' = ' . $workingTime->name,
                'is_legend' => true,
                'working_time' => $workingTime
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        $headings = ['Nama', 'Divisi', 'Jabatan'];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $headings[] = $date->format('j'); // tanggal 1,2,3...
        }

        return $headings;
    }

    public function map($item): array
    {
        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        // Jika ini adalah user (memiliki id property)
        if (isset($item->id) && !isset($item->is_separator) && !isset($item->is_legend) && !isset($item->is_legend_header) && !isset($item->is_month_header)) {
            $row = [
                $item->name,
                $item->department?->name ?? '-',
                $item->position?->name ?? '-'
            ];

            foreach ($period as $date) {
                $schedule = WorkSchedule::where('user_id', $item->id)
                                ->whereDate('work_date', $date)
                                ->first();
                
                // Tampilkan code jika ada schedule, '-' jika tidak ada
                if ($schedule && $schedule->workingTime) {
                    $row[] = $schedule->workingTime->code;
                } else {
                    $row[] = '-'; // Tanda '-' untuk yang tidak ada schedule
                }
            }

            return $row;
        }
        
        // Untuk header bulan, hanya isi kolom pertama
        if (isset($item->is_month_header)) {
            return [$item->name];
        }
        
        // Untuk baris separator, legend header, atau legend
        $row = [$item->name, '', ''];
        
        // Isi kolom tanggal dengan kosong untuk baris keterangan
        foreach ($period as $date) {
            $row[] = '';
        }
        
        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
                $end = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();
                $period = CarbonPeriod::create($start, $end);

                $totalUsers = count($this->users);
                $totalCols = count($period) + 3; // +3 untuk kolom nama, divisi, jabatan
                $dataEndRow = $totalUsers + 1; // +1 untuk header

                // Style header - center alignment dan border
                $headerRange = 'A1:' . $sheet->getCellByColumnAndRow($totalCols, 1)->getCoordinate();
                $sheet->getStyle($headerRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'font' => ['bold' => true],
                ]);

                $rowIndex = 2; // Mulai dari baris 2 (setelah header)

                // Style untuk data users
                foreach ($this->users as $user) {
                    // Style untuk kolom nama (left align)
                    $nameCell = $sheet->getCellByColumnAndRow(1, $rowIndex);
                    $nameCell->getStyle()->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    // Style untuk kolom divisi (left align)
                    $divisionCell = $sheet->getCellByColumnAndRow(2, $rowIndex);
                    $divisionCell->getStyle()->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    // Style untuk kolom jabatan (left align)
                    $positionCell = $sheet->getCellByColumnAndRow(3, $rowIndex);
                    $positionCell->getStyle()->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    foreach ($period as $colIndex => $date) {
                        $schedule = WorkSchedule::where('user_id', $user->id)
                                        ->whereDate('work_date', $date)
                                        ->first();
                        
                        $cell = $sheet->getCellByColumnAndRow($colIndex + 4, $rowIndex); // +4 karena nama, divisi, jabatan
                        
                        // Style dasar untuk cell (center alignment dan border)
                        $cell->getStyle()->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                ],
                            ],
                        ]);

                        // Background color jika ada schedule
                        if ($schedule && $schedule->workingTime && $schedule->workingTime->color) {
                            $cell->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($schedule->workingTime->color);
                        }
                    }
                    $rowIndex++;
                }
                
                // Skip baris separator (tanpa border)
                $rowIndex++;
                
                // Style untuk header bulan (tanpa border, bold)
                $monthCell = $sheet->getCellByColumnAndRow(1, $rowIndex);
                $monthCell->getStyle()->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $rowIndex++;
                
                // Skip baris separator kedua
                $rowIndex++;
                
                // Style untuk header keterangan (tanpa border)
                $cell = $sheet->getCellByColumnAndRow(1, $rowIndex);
                $cell->getStyle()->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $rowIndex++;
                
                // Style untuk setiap keterangan working time (tanpa border dan tanpa background)
                foreach ($this->workingTimes as $workingTime) {
                    $cell = $sheet->getCellByColumnAndRow(1, $rowIndex);
                    
                    // Style dasar untuk keterangan (tanpa background color)
                    $cell->getStyle()->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    
                    // TIDAK ada background color untuk keterangan (transparent)
                    
                    $rowIndex++;
                }
                
                // Auto-size kolom
                $sheet->getColumnDimension('A')->setAutoSize(true); // Nama
                $sheet->getColumnDimension('B')->setAutoSize(true); // Divisi
                $sheet->getColumnDimension('C')->setAutoSize(true); // Jabatan

                // Set lebar kolom tanggal agar pas
                for ($col = 4; $col <= $totalCols; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setWidth(4);
                }
            }
        ];
    }
}