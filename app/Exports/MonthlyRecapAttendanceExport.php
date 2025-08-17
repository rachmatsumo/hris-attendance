<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\WorkSchedule;
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

    public function title(): string
    {
        return 'Detail';
    }

    public function collection()
    {
        $start = now()->createFromFormat('Y-m', $this->month)->startOfMonth();
        $end = now()->createFromFormat('Y-m', $this->month)->endOfMonth();

        // return Attendance::with([
        //         'user.department',
        //         'user.position',
        //         'workSchedule.workingTime'
        //     ])
        //     ->whereBetween('date', [$start, $end])
        //     ->orderBy('date')
        //     ->orderBy('user_id')
        //     ->get();
        return WorkSchedule::with([
                                    'user.department',
                                    'user.position',
                                    'workingTime',
                                    'attendance' => function($q) use ($start, $end) {
                                        $q->whereBetween('date', [$start, $end]);
                                    }
                                ])
                                ->whereBetween('work_date', [$start, $end])
                                ->whereNotNull('working_time_id')
                                ->orderBy('work_date')
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
        // $user = $attendance->user;
        // $workingTime = $attendance->workSchedule?->workingTime;

        // $jadwal = $attendance->workingTime->schedule; 

        // // Cek attendancePermit terkait user untuk bulan ini
        // $permit = $attendance->user->attendancePermits()
        //                 ->where('status', 'approved')
        //                 ->whereDate('start_date', '<=', $attendance->work_date)
        //                 ->whereDate('end_date', '>=', $attendance->work_date)
        //                 ->first();

        // if ($permit) {
        //     // $jadwal = $permit->type === 'leave' ? 'Cuti' : 'Izin';
        //     $status = $permit->type === 'leave' ? 'Cuti' : 'Izin';
        // } else {
        //     $status = $attendance?->attendance->status ?? 'Tidak Hadir';
        // }

        // return [
        //     $this->counter++,                          // No
        //     $attendance->date,        // Tanggal
        //     $user->employee_id ?? '-',                 // ID Pegawai
        //     $user->name ?? '-',                         // Nama
        //     $user->department?->name ?? '-',           // Divisi
        //     $user->position?->name ?? '-',             // Jabatan
        //     $jadwal,                                   // Jadwal Kerja
        //     $attendance?->attendance->clock_in_time ?? '-',
        //     $attendance?->attendance->clock_out_time ?? '-',
        //     ucwords($status),                                   // Status
        // ];
        return [];
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
                    $jadwal = $attendance->workingTime->schedule; 
                    // dd($attendance);

                    // Cek attendancePermit terkait user untuk bulan ini
                    $permit = $attendance->user->attendancePermits()
                                    ->where('status', 'approved')
                                    ->whereDate('start_date', '<=', $attendance->work_date)
                                    ->whereDate('end_date', '>=', $attendance->work_date)
                                    ->first();

                    if ($permit) {
                        // $jadwal = $permit->type === 'leave' ? 'Cuti' : 'Izin';
                        $status = $permit->type === 'leave' ? 'Cuti' : 'Izin';
                    } else {
                        $status = $attendance?->attendance->status ?? 'Tidak Hadir';
                    }
                    
                    $rowData = [
                        $counter++,
                        $attendance->work_date,
                        $user->employee_id ?? '-',
                        $user->name ?? '-',
                        $user->department?->name ?? '-',
                        $user->position?->name ?? '-',
                        $jadwal,
                        $attendance?->attendance->clock_in_time ?? '-',
                        $attendance?->attendance->clock_out_time ?? '-',
                        ucwords($status),
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