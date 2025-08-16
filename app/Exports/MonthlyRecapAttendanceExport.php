<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class MonthlyRecapAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $month;
    protected $counter = 1;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection()
    {
        $start = now()->createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = now()->createFromFormat('Y-m', $this->month)->endOfMonth();

        return Attendance::with([
                'user.department',
                'user.position',
                'workSchedule.workingTime'
            ])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('user_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'ID Pegawai',
            'Nama',
            'Divisi',
            'Jabatan',
            'Jadwal Kerja',
            'Clock In',
            'Clock Out',
            'Status',
        ];
    }

    public function map($attendance): array
    {
        $user = $attendance->user;
        $workingTime = $attendance->workSchedule?->workingTime;

        $jadwal = $workingTime 
            ? $workingTime->start_time . ' - ' . $workingTime->end_time 
            : '-';

        return [
            $this->counter++,                          // No
            $attendance->date->format('Y-m-d'),        // Tanggal
            $user->employee_id ?? '-',                 // ID Pegawai
            $user->name ?? '-',                         // Nama
            $user->department?->name ?? '-',           // Divisi
            $user->position?->name ?? '-',             // Jabatan
            $jadwal,                                   // Jadwal Kerja
            $attendance->clock_in_time ?? '-',         // Clock In
            $attendance->clock_out_time ?? '-',        // Clock Out
            $attendance->status ?? '-',                // Status
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $periode = Carbon::createFromFormat('Y-m', $this->month)
                                ->locale('id')
                                ->translatedFormat('F Y'); 
                
                // Baris 1: Periode Bulan
                $sheet->setCellValue('A1', 'Periode Bulan : ' . $periode);
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Baris 2: Waktu Download
                $downloadTime = now()->format('Y-m-d H:i:s');
                $sheet->setCellValue('A2', 'Waktu Download : ' . $downloadTime);
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Baris 3: Kosong (tidak perlu diisi, sudah kosong secara default)

                // Baris 4: Header tabel (dimulai dari baris 4)
                $headings = [
                    'No', 'Tanggal', 'ID Pegawai', 'Nama', 'Divisi', 
                    'Jabatan', 'Jadwal Kerja', 'Clock In', 'Clock Out', 'Status'
                ];
                
                foreach($headings as $index => $heading) {
                    $column = chr(65 + $index); // A, B, C, dst
                    $sheet->setCellValue($column . '4', $heading);
                }

                // Shift data ke baris 5 dan seterusnya
                $dataStartRow = 5;
                $collection = $this->collection();
                $counter = 1;
                
                foreach($collection as $rowIndex => $attendance) {
                    $user = $attendance->user;
                    $workingTime = $attendance->workSchedule?->workingTime;
                    $jadwal = $workingTime 
                        ? $workingTime->start_time . ' - ' . $workingTime->end_time 
                        : '-';
                    
                    $rowData = [
                        $counter++,
                        $attendance->date->format('Y-m-d'),
                        $user->employee_id ?? '-',
                        $user->name ?? '-',
                        $user->department?->name ?? '-',
                        $user->position?->name ?? '-',
                        $jadwal,
                        $attendance->clock_in_time ?? '-',
                        $attendance->clock_out_time ?? '-',
                        $attendance->status ?? '-',
                    ];
                    
                    foreach($rowData as $colIndex => $value) {
                        $column = chr(65 + $colIndex);
                        $sheet->setCellValue($column . ($dataStartRow + $rowIndex), $value);
                    }
                }

                // Atur border dan alignment untuk tabel (mulai dari baris 4)
                $highestRow = $sheet->getHighestRow();
                $highestColumn = 'J'; // Karena ada 10 kolom (A-J)
                
                $sheet->getStyle("A4:{$highestColumn}{$highestRow}")
                      ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Auto size kolom agar lebarnya fit ke teks
                foreach(range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Heading alignment center (baris 4)
                $sheet->getStyle("A4:{$highestColumn}4")
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}