<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSchedule;
use App\Models\User;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Exports\MonthlyScheduleExport;
use Maatwebsite\Excel\Facades\Excel;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        // $employees = User::with('workSchedules')->get();
        // return view('admin.resource_management.work_schedule', compact('employees'));
        $month = $request->month ?? date('Y-m'); 
        // Ambil data work_schedule grouped by bulk_id
        $bulkSchedules = WorkSchedule::select('bulk_id')
            ->whereYear('work_date', explode('-', $month)[0])
            ->whereMonth('work_date', explode('-', $month)[1])
            ->groupBy('bulk_id')
            ->get();

        $data = $bulkSchedules->map(function($bulk) {
            $schedules = WorkSchedule::with('user', 'workingTime')
                ->where('bulk_id', $bulk->bulk_id)
                ->get();

            $users = $schedules->pluck('user.name')->unique();
            $userCount = $users->count();

            $totalHariKerja = $schedules->whereNotNull('working_time_id')->count();
            $totalHariKerjaPerUser = $userCount ? $totalHariKerja / $userCount : 0;

            $totalHariLibur = $schedules->whereNull('working_time_id')->count();
            $totalHariLiburPerUser = $userCount ? $totalHariLibur / $userCount : 0;

            $firstDay = $schedules->sortBy('work_date')->first();
            $lastDay  = $schedules->sortBy('work_date')->last();

            return [
                'bulk_id' => $bulk->bulk_id,
                'karyawan' => $users->implode(', '),
                'total_hari_kerja' => (int)$totalHariKerjaPerUser,
                'total_hari_libur' => (int)$totalHariLiburPerUser,
                'hari_pertama' => $firstDay ? ($firstDay->workingTime?->name ?? 'Libur') : null,
                'hari_terakhir' => $lastDay ? ($lastDay->workingTime?->name ?? 'Libur') : null,
            ];
        });

        return view('admin.resource_management.work_schedule', compact('data', 'month'));
    }

    public function show($bulk_id)
    {
        // Ambil semua jadwal dengan bulk_id
        $schedules = WorkSchedule::with('user', 'workingTime')
            ->where('bulk_id', $bulk_id)
            ->orderBy('work_date')
            ->get();

        if ($schedules->isEmpty()) {
            abort(404, 'Bulk schedule tidak ditemukan.');
        }

        // Ambil semua user unik
        $users = $schedules->pluck('user')->unique('id');

        // Ambil semua tanggal unik
        $dates = $schedules->pluck('work_date')->unique()->sort()->values();
 

        return view('admin.resource_management.work_schedule_show_bulk', compact('bulk_id', 'schedules', 'users', 'dates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'days' => 'required|array',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        foreach ($request->days as $day) {
            WorkSchedule::updateOrCreate(
                ['employee_id' => $request->employee_id, 'day_of_week' => $day],
                ['start_time' => $request->start_time, 'end_time' => $request->end_time]
            );
        }

        return back()->with('success', 'Jadwal berhasil disimpan!');
    } 

    public function destroy(WorkSchedule $work_schedule)
    { 
        $work_schedule->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }

    public function batchCreate()
    { 
        $users = User::select('id', 'name')->orderBy('name')->get();
        $workingTimes = WorkingTime::orderBy('name')->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'users' => $users,
                'working_times' => $workingTimes
            ]);
        }

        $shiftPattern = [null]; // mulai dengan 1 pola Libur
        $month = now()->format('Y-m');
        $startDay = 1; // Senin

        return view('admin.resource_management.work_schedule_create_bulk', compact('users', 'workingTimes', 'shiftPattern', 'month', 'startDay'));
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m', // YYYY-MM
            'users' => 'required|array',
            'shift_pattern' => 'required|array', // array working_time_id atau null
            'start_day' => 'required|integer|min:0|max:6', // 0=Sunday … 6=Saturday
        ]);

        $month = $request->month;
        $users = $request->users;
        $shiftPattern = $request->shift_pattern;
        $startDay = (int) $request->start_day;
        $patternLength = count($shiftPattern);

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        // Tentukan tanggal pertama yang sesuai start_day
        $firstShiftDate = $start->copy();
        while ($firstShiftDate->dayOfWeek != $startDay) {
            $firstShiftDate->addDay();
        }

        // Hitung offset dari tanggal awal bulan sampai firstShiftDate
        $offset = $start->diffInDays($firstShiftDate);

        // Buat pola shift untuk full bulan
        $fullMonthPattern = [];
        for ($i = 0; $i < $daysInMonth; $i++) {
            $shiftIndex = (($i - $offset) % $patternLength + $patternLength) % $patternLength;
            $fullMonthPattern[] = $shiftPattern[$shiftIndex] ?? 'null';
        }

        // Buat bulk_id deterministik
        $patternString = $month . implode(',', $fullMonthPattern) . $startDay;
        $bulkId = md5($patternString);

        $period = CarbonPeriod::create($start, $end);
        $insertData = [];

        foreach ($period as $index => $date) {
            // Hitung indeks shift (modulus aman)
            $shiftId = $fullMonthPattern[$index] === 'null' ? null : $fullMonthPattern[$index];

            foreach ($users as $userId) {
                $insertData[] = [
                    'user_id' => $userId,
                    'work_date' => $date->toDateString(),
                    'working_time_id' => $shiftId,
                    'is_active' => true,
                    'bulk_id' => $bulkId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Hapus dulu jadwal yang sama (bulk_id sama) supaya tidak duplikat
        // WorkSchedule::where('bulk_id', $bulkId)->delete();

        // Batch insert
        WorkSchedule::insert($insertData);

        return redirect()->route('work-schedule.index')
                        ->with('success', 'Jadwal kerja berhasil dibuat untuk bulan ' . $start->format('F Y') . ' sesuai pola dan start day.');
    }

    public function batchEdit($bulk_id)
    {
        // Ambil semua jadwal sesuai bulk_id
        $schedules = WorkSchedule::with('workingTime')->where('bulk_id', $bulk_id)->orderBy('work_date')->get();

        if($schedules->isEmpty()) {
            return redirect()->route('work-schedule.index')->with('error', 'Bulk schedule tidak ditemukan');
        }

        // Ambil bulan dari jadwal
        $month = Carbon::parse($schedules->first()->work_date)->format('Y-m');

        // Ambil users unik dari jadwal
        // $users = $schedules->pluck('user')->unique('id');
        $allUsers = User::active()->get(); // atau sesuai kebutuhan
        $selectedUserIds = $schedules->pluck('user_id')->toArray();

        // Ambil workingTimes
        $workingTimes = WorkingTime::where('is_active', true)->get();

        // Buat pola shift per tanggal untuk prefill form
        $dates = $schedules->pluck('work_date')->unique()->sort()->values();
        $shiftPattern = [];

        // Ambil start_day dari bulk (anggap hari pertama pola sesuai tanggal pertama)
        $startDay = Carbon::parse($dates->first())->dayOfWeek;

        // Buat shift pattern prefill (ambil 1 user sebagai referensi)
        $firstUserId = $selectedUserIds[0];
        $userSchedules = $schedules->where('user_id', $schedules->first()->user_id)
                           ->pluck('working_time_id')
                           ->toArray();

        // Fungsi untuk cari pola berulang terkecil
        function detectPattern(array $arr) {
            $len = count($arr);
            for ($p = 1; $p <= $len; $p++) {
                $pattern = array_slice($arr, 0, $p);
                $matches = true;
                for ($i = 0; $i < $len; $i++) {
                    if ($arr[$i] !== $pattern[$i % $p]) {
                        $matches = false;
                        break;
                    }
                }
                if ($matches) return $pattern;
            }
            return $arr; // jika tidak ada pola, kembalikan semua
        }

        $shiftPattern = detectPattern($userSchedules); // pola dasar, bisa panjang 1,2,3, dll

        // dd($shiftPattern);

        return view('admin.resource_management.work_schedule_edit_bulk', compact(
            'bulk_id', 'month', 'allUsers', 'selectedUserIds', 'workingTimes', 'shiftPattern', 'startDay'
        ));
    }

    public function batchUpdate(Request $request, $bulk_id)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'users' => 'required|array',
            'shift_pattern' => 'required|array',
            'start_day' => 'required|integer|min:0|max:6',
        ]);

        $month = $request->month;
        $users = $request->users;
        $shiftPattern = array_map(fn($v) => $v === "" ? null : (int)$v, $request->shift_pattern);
        $startDay = (int) $request->start_day;
        $patternLength = count($shiftPattern);

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $firstShiftDate = $start->copy();
        while ($firstShiftDate->dayOfWeek != $startDay) {
            $firstShiftDate->addDay();
        }

        $offset = $start->diffInDays($firstShiftDate);

        $fullMonthPattern = [];
        $daysInMonth = $start->daysInMonth;
        for ($i = 0; $i < $daysInMonth; $i++) {
            $shiftIndex = (($i - $offset) % $patternLength + $patternLength) % $patternLength;
            $fullMonthPattern[] = $shiftPattern[$shiftIndex] ?? null;
        }

        // Buat bulk_id baru dari pola
        $patternString = $month . implode(',', $fullMonthPattern) . $startDay;
        $newBulkId = md5($patternString);

        // Ambil jadwal lama sesuai bulk_id lama
        $existingSchedules = WorkSchedule::where('bulk_id', $bulk_id)->get();

        $existingUserIds = $existingSchedules->pluck('user_id')->unique()->toArray();

        // 1. Update bulk_id untuk user lama
        WorkSchedule::whereIn('user_id', $users)
            ->where('bulk_id', $bulk_id)
            ->update(['bulk_id' => $newBulkId]);

        // 2. Hapus jadwal untuk user yang dihapus, kecuali yang sudah ada attendance
        $usersToDelete = array_diff($existingUserIds, $users);
        WorkSchedule::whereIn('user_id', $usersToDelete)
            ->where('bulk_id', $bulk_id)
            ->doesntHave('attendance')
            ->delete();

        // 3. Tambahkan jadwal untuk user baru
        $usersToInsert = array_diff($users, $existingUserIds);
        if (!empty($usersToInsert)) {
            $period = CarbonPeriod::create($start, $end);
            $insertData = [];

            foreach ($period as $index => $date) {
                $shiftId = $fullMonthPattern[$index];
                if ($shiftId === '0' || $shiftId === 0 || $shiftId === 'null' || $shiftId === null || $shiftId === '') {
                    $shiftId = null;
                }

                foreach ($usersToInsert as $userId) {
                    $insertData[] = [
                        'user_id' => $userId,
                        'work_date' => $date->toDateString(),
                        'working_time_id' => $shiftId,
                        'is_active' => true,
                        'bulk_id' => $newBulkId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            WorkSchedule::insert($insertData);
        }

        return redirect()->route('work-schedule.show', $newBulkId)
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroyBulk($bulk_id)
    {
        // Hapus jadwal yang belum punya attendance
        $deletedCount = WorkSchedule::where('bulk_id', $bulk_id)
            ->doesntHave('attendance')
            ->delete();

        return redirect()->route('work-schedule.index')
                        ->with('success', "$deletedCount jadwal berhasil dihapus (yang sudah ada attendance tidak terhapus).");
    }
 
    public function exportMonthly(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m'); 
        $timestamp = now()->format('dHis');

        $name = $month . '__' . $timestamp;
        return Excel::download(new MonthlyScheduleExport($month), "Schedule_$name.xlsx");
    }

}
