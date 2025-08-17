<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class MonthlyRecapAttendanceSummarySheet implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $month;
    protected $users;

    public function __construct($month)
    {
        $this->month = $month;
        $this->users = User::active()->get();
    }

    public function title(): string
    {
        return 'Summary'; 
    }

    public function collection()
    {
        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        $data = collect();

        foreach ($this->users as $user) {
            $schedules = WorkSchedule::where('user_id', $user->id)
                            ->whereBetween('work_date', [$start, $end])
                            ->whereNotNull('working_time_id')
                            ->get();

            $attendance = $user->attendances()
                            ->whereBetween('date', [$start, $end])
                            ->get();

            $permit = $user->attendancePermits()
                            ->where('status', 'approved')
                            ->where(function($q) use ($start, $end) {
                                $q->whereBetween('start_date', [$start, $end])
                                  ->orWhereBetween('end_date', [$start, $end]);
                            })->get();

            $masuk = $attendance->where('status', 'present')->count();
            $terlambat = $attendance->where('status', 'late')->count();
            $alpa = $attendance->where('status', 'absent')->count();
            $izin = $permit->where('type', '!=', 'leave')->count();
            $cuti = $permit->where('type', 'leave')->count();

            if($schedules->count() !== 0){
                $data->push((object)[
                    'id' => $user->employee_id,
                    'name' => $user->name,
                    'position' => $user->position?->name . ' - ' . $user->department?->name,
                    'jumlah_schedule' => $schedules->count(),
                    'masuk' => $masuk,
                    'terlambat' => $terlambat,
                    'alpa' => $alpa,
                    'izin' => $izin,
                    'cuti' => $cuti
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        // Return empty array karena kita akan handle header secara manual di registerEvents
        return [];
    }

    public function map($item): array
    {
        // Return empty array karena kita akan handle data secara manual di registerEvents
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Format periode bulan dalam bahasa Indonesia
                $periode = Carbon::createFromFormat('Y-m', $this->month)
                                ->locale('id')
                                ->translatedFormat('F Y'); 
                
                // Baris 1: Periode Bulan
                $sheet->setCellValue('A1', 'Periode : ' . $periode);
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

                // Baris 2: Waktu Download
                $downloadTime = now()->format('Y-m-d H:i:s');
                $sheet->setCellValue('A2', 'Waktu Download : ' . $downloadTime);
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

                // Baris 3: Kosong (tidak perlu diisi)

                // Baris 4: Header tabel
                $headings = [
                    'No', 'ID', 'Nama', 'Jabatan', 'Jumlah Schedule', 
                    'Masuk', 'Terlambat', 'Alpa', 'Izin', 'Cuti'
                ];
                
                foreach($headings as $index => $heading) {
                    $column = chr(65 + $index); // A, B, C, dst
                    $sheet->setCellValue($column . '4', $heading);
                }

                // Style untuk header (baris 4)
                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E2EFDA'
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Baris 5 dan seterusnya: Data tabel
                $dataStartRow = 5;
                $collection = $this->collection();
                $counter = 1;
                
                foreach($collection as $rowIndex => $item) {
                    $rowData = [
                        $counter++,
                        $item->id,
                        $item->name,
                        $item->position,
                        $item->jumlah_schedule,
                        $item->masuk,
                        $item->terlambat,
                        $item->alpa,
                        $item->izin,
                        $item->cuti
                    ];
                    
                    $currentRow = $dataStartRow + $rowIndex;
                    foreach($rowData as $colIndex => $value) {
                        $column = chr(65 + $colIndex);
                        $sheet->setCellValue($column . $currentRow, $value);
                    }
                }

                // Atur border untuk seluruh tabel data (mulai dari baris 4)
                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'J'; // Karena ada 10 kolom (A-J)
                
                // Border untuk data (baris 5 ke bawah)
                if ($highestRow >= 5) {
                    $sheet->getStyle("A5:{$highestColumn}{$highestRow}")
                          ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    
                    // Alignment untuk data
                    $sheet->getStyle("A5:{$highestColumn}{$highestRow}")
                          ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Center alignment untuk kolom angka (No, Jumlah Schedule, Masuk, Terlambat, Alpa, Izin, Cuti)
                    $sheet->getStyle("A5:A{$highestRow}")
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $sheet->getStyle("E5:J{$highestRow}")
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Left alignment untuk kolom teks (ID, Nama, Jabatan)
                    $sheet->getStyle("B5:D{$highestRow}")
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                // Set lebar kolom yang fit dengan konten
                $sheet->getColumnDimension('A')->setWidth(5);   // No
                $sheet->getColumnDimension('B')->setWidth(12);  // ID
                $sheet->getColumnDimension('C')->setWidth(25);  // Nama
                $sheet->getColumnDimension('D')->setWidth(30);  // Jabatan
                $sheet->getColumnDimension('E')->setWidth(15);  // Jumlah Schedule
                $sheet->getColumnDimension('F')->setWidth(8);   // Masuk
                $sheet->getColumnDimension('G')->setWidth(10);  // Terlambat
                $sheet->getColumnDimension('H')->setWidth(8);   // Alpa
                $sheet->getColumnDimension('I')->setWidth(8);   // Izin
                $sheet->getColumnDimension('J')->setWidth(8);   // Cuti

                // Set row height untuk header
                $sheet->getRowDimension(4)->setRowHeight(25);
                
                // Set row height untuk data rows
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                // Tambahkan styling alternating rows untuk data
                for ($row = 5; $row <= $highestRow; $row++) {
                    if (($row - 5) % 2 == 1) { // Baris ganjil (mulai dari baris ke-6, 8, 10, dst)
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'F8F9FA'
                                ]
                            ]
                        ]);
                    }
                }
            },
        ];
    }
}