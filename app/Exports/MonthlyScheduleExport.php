<?php

namespace App\Exports;

use App\Models\WorkSchedule;
use App\Models\User;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $data = collect();

        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        // Ambil user yang punya schedule di bulan ini
        $userIdsWithSchedule = WorkSchedule::whereBetween('work_date', [$start, $end])
                                ->pluck('user_id')
                                ->unique();

        $usersWithSchedule = $this->users->whereIn('id', $userIdsWithSchedule);

        foreach ($usersWithSchedule as $user) {
            $data->push($user);
        }

        // Baris kosong
        $data->push((object)['name' => '', 'is_separator' => true]);

        // Header bulan
        $monthName = $start->locale('id')->translatedFormat('F Y');
        $data->push((object)['name' => 'Bulan : ' . $monthName, 'is_month_header' => true]);

        $data->push((object)['name' => '', 'is_separator' => true]);

        // Keterangan legend
        $data->push((object)['name' => 'Keterangan:', 'is_legend_header' => true]);
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
        $end   = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        $headings = ['Nama', 'Divisi', 'Jabatan'];
        foreach (CarbonPeriod::create($start, $end) as $date) {
            $headings[] = $date->format('j');
        }

        return $headings;
    }

    public function map($item): array
    {
        $start = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

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

                $permit = $schedule?->attendancePermit;

                if ($permit) {
                    $row[] = $permit->type === 'leave' ? 'Cuti' : 'Izin';
                } elseif ($schedule && $schedule->workingTime) {
                    $row[] = $schedule->workingTime->code;
                } else {
                    $row[] = '-';
                }
            }

            return $row;
        }

        $row = [$item->name, '', ''];
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
                $end   = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();
                $period = CarbonPeriod::create($start, $end);
                $totalCols = count($period) + 3;

                // Style header
                $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow($totalCols, 1)->getCoordinate())->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => ['bold' => true],
                ]);

                $rowIndex = 2;

                foreach ($this->users as $user) {
                    $userScheduleCount = WorkSchedule::where('user_id', $user->id)
                        ->whereBetween('work_date', [$start, $end])
                        ->count();

                    if ($userScheduleCount === 0) continue; // skip user tanpa schedule

                    foreach ([1,2,3] as $col) {
                        $sheet->getCellByColumnAndRow($col, $rowIndex)->getStyle()->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);
                    }

                    $colIndex = 4;
                    $prevValue = null;
                    $mergeStartCol = $colIndex;

                    foreach ($period as $date) {
                        $schedule = WorkSchedule::where('user_id', $user->id)
                                        ->whereDate('work_date', $date)
                                        ->first();

                        $permit = $schedule?->attendancePermit;

                        if ($permit) {
                            $value = $permit->type === 'leave' ? 'Cuti' : 'Izin';
                        } elseif ($schedule && $schedule->workingTime) {
                            $value = $schedule->workingTime->code;
                        } else {
                            $value = '-';
                        }

                        $cell = $sheet->getCellByColumnAndRow($colIndex, $rowIndex);
                        $cell->setValue($value);

                        $cell->getStyle()->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        ]);

                        // Background shift color (skip Cuti/Izin)
                        if ($schedule && $schedule->workingTime && $schedule->workingTime->color && !in_array($value, ['Cuti','Izin'])) {
                            $cell->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($schedule->workingTime->color);
                        }

                        // Merge cell berturut-turut
                        if ($prevValue === null) {
                            $prevValue = $value;
                            $mergeStartCol = $colIndex;
                        } elseif ($value !== $prevValue) {
                            if ($mergeStartCol < $colIndex - 1) {
                                $sheet->mergeCellsByColumnAndRow($mergeStartCol, $rowIndex, $colIndex - 1, $rowIndex);
                            }
                            $mergeStartCol = $colIndex;
                            $prevValue = $value;
                        }

                        $colIndex++;
                    }

                    if ($mergeStartCol < $colIndex - 1) {
                        $sheet->mergeCellsByColumnAndRow($mergeStartCol, $rowIndex, $colIndex - 1, $rowIndex);
                    }

                    $rowIndex++;
                }

                // Auto-size Nama, Divisi, Jabatan
                foreach (range('A','C') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                for ($col=4; $col<=$totalCols; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setWidth(6);
                }
            }
        ];
    }
}
