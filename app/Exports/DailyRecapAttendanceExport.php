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

class DailyRecapAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $date;
    protected $counter = 1;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {   
        $date = $this->date;
        return WorkSchedule::with([
                                'user.department',
                                'user.position',
                                'workingTime',
                                'attendance' => function($q) use ($date) {
                                    $q->whereDate('date', $date);
                                }
                            ])
                            ->whereDate('work_date', $date)
                            ->whereNotNull('working_time_id')
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
        // $user = $attendance->user;
        // $workingTime = $attendance->workSchedule?->workingTime;

        // $jadwal = $workingTime 
        //     ? $workingTime->start_time . ' - ' . $workingTime->end_time 
        //     : '-';

        // // Cek attendancePermit terkait user untuk tanggal ini
        // $permit = $attendance->user->attendancePermits()
        //                 ->where('status', 'approved')
        //                 ->whereDate('start_date', '<=', $attendance->work_date)
        //                 ->whereDate('end_date', '>=', $attendance->work_date)
        //                 ->first();

        // // dd($permit);

        // if ($permit) {
        //     // $jadwal = $permit->type === 'leave' ? 'Cuti' : 'Izin';
        //     $status = $permit->type === 'leave' ? 'Cuti' : 'Izin';
        // } else {
        //     $status = $attendance->status ?? 'Tidak Hadir';
        // }
        // // dd($status);

        // return [
        //     $this->counter++,                          // No
        //     $attendance->work_date,        // Tanggal
        //     $user->employee_id ?? '-',                 // ID Pegawai
        //     $user->name ?? '-',                        // Nama
        //     $user->department?->name ?? '-',           // Divisi
        //     $user->position?->name ?? '-',             // Jabatan
        //     $jadwal,                                   // Jadwal Kerja
        //     $attendance->clock_in_time ?? '-',         // Clock In
        //     $attendance->clock_out_time ?? '-',        // Clock Out
        //     $status,                                   // Status
        // ];
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Baris 1: Periode Hari
                $periodeHari = Carbon::parse($this->date)
                                ->locale('id')
                                ->translatedFormat('l, j F Y');
                $sheet->setCellValue('A1', 'Periode Hari : ' . $periodeHari);
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

                    // Cek attendancePermit terkait user untuk tanggal ini
                    $permit = $attendance->user->attendancePermits()
                                    ->where('status', 'approved')
                                    ->whereDate('start_date', '<=', $attendance->work_date)
                                    ->whereDate('end_date', '>=', $attendance->work_date)
                                    ->first();

                    // dd($permit);

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