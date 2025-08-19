<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('workSchedule')
                    ->where('user_id', Auth::id())
                    ->orderBy('date', 'desc')
                    ->paginate(5);

        $locations = Location::where('is_active', true)->get();

        $now = Carbon::now();

        // Ambil jadwal dari 2 hari sebelumnya hingga hari ini untuk mengcover shift malam
        $schedules = WorkSchedule::with('workingTime')
            ->where('user_id', Auth::id())
            ->whereIn('work_date', [
                today()->subDays(2), 
                today()->subDay(), 
                today()
            ])
            ->get();

        // Cari shift yang sedang aktif berdasarkan waktu sekarang
        $activeShifts = $schedules->filter(function($schedule) use ($now) {
            if (!$schedule->workingTime) return false;

            $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                ->setDateFrom($schedule->work_date);
            $shiftEnd = Carbon::parse($schedule->workingTime->end_time)
                                ->setDateFrom($schedule->work_date);

            if ($schedule->workingTime->end_next_day) {
                $shiftEnd->addDay();
            }

            // Extended window untuk absensi
            $clockInStart = $shiftStart->copy()->subHours(2);
            $clockOutEnd = $shiftEnd->copy()->addHours(5);

            return $now->between($clockInStart, $clockOutEnd);
        });

        // Prioritaskan shift yang sedang berlangsung, bukan yang akan datang
        $currentlyRunning = $activeShifts->filter(function($schedule) use ($now) {
            $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                ->setDateFrom($schedule->work_date);
            $shiftEnd = Carbon::parse($schedule->workingTime->end_time)
                                ->setDateFrom($schedule->work_date);
            
            if ($schedule->workingTime->end_next_day) {
                $shiftEnd->addDay();
            }

            return $now->between($shiftStart, $shiftEnd);
        });

        // Jika ada shift yang sedang berlangsung, gunakan itu
        if ($currentlyRunning->isNotEmpty()) {
            $activeShift = $currentlyRunning->first();
        } else {
            // Jika tidak ada yang sedang berlangsung, cari yang paling dekat
            $activeShift = $activeShifts->sortBy(function($schedule) use ($now) {
                $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                    ->setDateFrom($schedule->work_date);
                
                return abs($now->diffInMinutes($shiftStart));
            })->first();
        }

        // Fallback: jika masih tidak ada, ambil shift terdekat dari semua jadwal
        if (!$activeShift) {
            $activeShift = $schedules->sortBy(function($schedule) use ($now) {
                if (!$schedule->workingTime) return PHP_INT_MAX;

                $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                    ->setDateFrom($schedule->work_date);
                
                return abs($now->diffInMinutes($shiftStart));
            })->first();
        }

        // Cari shift untuk display (prioritas shift hari ini atau yang akan datang)
        $displayShift = $schedules->filter(function($schedule) use ($now) {
            $workDate = Carbon::parse($schedule->work_date);
            return $workDate->isToday() || $workDate->isFuture();
        })->sortBy('work_date')->first();

        // Jika tidak ada shift hari ini/masa depan, gunakan active shift
        if (!$displayShift) {
            $displayShift = $activeShift;
        }

        $clockInWindow = null;
        $clockOutWindow = null;

        // Gunakan activeShift untuk window absensi, displayShift untuk tampilan jadwal
        if ($activeShift && $activeShift->workingTime) {
            $shiftStart = Carbon::parse($activeShift->workingTime->start_time)
                                ->setDateFrom($activeShift->work_date);
            $shiftEnd = Carbon::parse($activeShift->workingTime->end_time)
                                ->setDateFrom($activeShift->work_date);
            
            if ($activeShift->workingTime->end_next_day) {
                $shiftEnd->addDay();
            }

            $clockInWindow = [
                'start' => $shiftStart->copy()->subHours(2)->toISOString(),
                'end' => $shiftStart->copy()->addHours(2)->toISOString(),
            ];
            
            $clockOutWindow = [
                'start' => $shiftEnd->copy()->subMinutes(30)->toISOString(),
                'end' => $shiftEnd->copy()->addHours(5)->toISOString(),
            ];
        }

        // Untuk debugging - bisa dihapus setelah testing
        // dd([
        //     'now' => $now->toDateTimeString(),
        //     'activeShift' => $activeShift ? [
        //         'date' => Carbon::parse($activeShift->work_date)->toDateString(),
        //         'time' => $activeShift->workingTime->start_time . ' - ' . $activeShift->workingTime->end_time,
        //         'end_next_day' => $activeShift->workingTime->end_next_day ?? false
        //     ] : null,
        //     'displayShift' => $displayShift ? [
        //         'date' => Carbon::parse($displayShift->work_date)->toDateString(), 
        //         'time' => $displayShift->workingTime->start_time . ' - ' . $displayShift->workingTime->end_time
        //     ] : null,
        //     'clockInWindow' => $clockInWindow,
        //     'clockOutWindow' => $clockOutWindow,
        //     'schedules' => $schedules->map(fn($s) => [
        //         'date' => Carbon::parse($s->work_date)->toDateString(),
        //         'time' => $s->workingTime->start_time . ' - ' . $s->workingTime->end_time,
        //         'end_next_day' => $s->workingTime->end_next_day ?? false
        //     ])
        // ]);

        return view('attendances.attendance', compact(
            'attendances', 
            'activeShift',        // Untuk window absensi (shift yang sedang berlangsung)
            'displayShift',       // Untuk tampilan jadwal (shift hari ini/akan datang) 
            'locations',
            'clockInWindow',
            'clockOutWindow'
        ));
    }
 
    public function store(Request $request)
    {
        $type = $request->input('type'); // clock_in_time / clock_out_time
        $locationRequired = (int) setting('location_required', 0);
        $photoClockInRequire = (int) setting('photo_required_clock_in', 0);
        $photoClockOutRequire = (int) setting('photo_required_clock_out', 0);
        
        // Validasi wajib foto
        if (!$request->photo) {
            if(($type=='clock_in_time' && $photoClockInRequire) || ($type=='clock_out_time' && $photoClockOutRequire)){
                return redirect()->back()->with('error', 'Foto wajib diambil sebelum clock in/out');
            }
        }

        $now = Carbon::now();
        
        // Ambil schedule dari 2 hari sebelumnya hingga hari ini untuk cover shift malam
        $schedules = WorkSchedule::with('workingTime')
            ->where('user_id', Auth::id())
            ->whereIn('work_date', [
                today()->subDays(2),
                today()->subDay(),
                today()
            ])
            ->orderBy('work_date', 'desc') // Urutkan dari yang terbaru
            ->get();

        // Cari semua schedule yang dalam window absensi
        $activeSchedules = collect();
        
        foreach ($schedules as $schedule) {
            if (!$schedule->workingTime) continue;

            $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                ->setDateFrom($schedule->work_date);
            $shiftEnd = Carbon::parse($schedule->workingTime->end_time)
                                ->setDateFrom($schedule->work_date);

            if ($schedule->workingTime->end_next_day) {
                $shiftEnd->addDay();
            }

            // Extended window untuk absensi
            $clockInStart = $shiftStart->copy()->subHours(2);
            $clockOutEnd = $shiftEnd->copy()->addHours(5);

            // Jika waktu sekarang dalam window absensi
            if ($now->between($clockInStart, $clockOutEnd)) {
                $activeSchedules->push($schedule);
            }
        }

        // Pilih schedule berdasarkan prioritas
        $activeSchedule = null;
        
        if ($activeSchedules->isNotEmpty()) {
            // LOGIC PRIORITAS UNTUK PEMILIHAN SCHEDULE:
            
            if ($type === 'clock_in_time') {
                // UNTUK CLOCK IN: Prioritaskan schedule yang belum ada clock_in
                $availableSchedules = $activeSchedules->filter(function($schedule) {
                    $attendance = Attendance::where('user_id', Auth::id())
                        ->where('date', Carbon::parse($schedule->work_date)->toDateString())
                        ->first();
                    
                    return !$attendance || !$attendance->clock_in_time;
                });
                
                // Jika ada schedule yang belum clock_in, pilih yang paling dekat dengan hari ini
                if ($availableSchedules->isNotEmpty()) {
                    $activeSchedule = $availableSchedules->sortByDesc('work_date')->first();
                } else {
                    // Jika semua sudah clock_in, pilih yang terbaru
                    $activeSchedule = $activeSchedules->sortByDesc('work_date')->first();
                }
                
            } else if ($type === 'clock_out_time') {
                // UNTUK CLOCK OUT: Prioritaskan schedule yang sudah clock_in tapi belum clock_out
                $pendingSchedules = $activeSchedules->filter(function($schedule) {
                    $attendance = Attendance::where('user_id', Auth::id())
                        ->where('date', Carbon::parse($schedule->work_date)->toDateString())
                        ->first();
                    
                    return $attendance && $attendance->clock_in_time && !$attendance->clock_out_time;
                });
                
                if ($pendingSchedules->isNotEmpty()) {
                    // Pilih yang sudah clock_in tapi belum clock_out
                    $activeSchedule = $pendingSchedules->sortByDesc('work_date')->first();
                } else {
                    // Fallback: pilih schedule terbaru yang dalam window
                    $activeSchedule = $activeSchedules->sortByDesc('work_date')->first();
                }
            }
        }

        // Fallback: jika tidak ada schedule aktif, cari yang terdekat
        if (!$activeSchedule) {
            $activeSchedule = $schedules->sortBy(function($schedule) use ($now) {
                if (!$schedule->workingTime) return PHP_INT_MAX;

                $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                                    ->setDateFrom($schedule->work_date);
                
                return abs($now->diffInMinutes($shiftStart));
            })->first();
        }

        if (!$activeSchedule) {
            return redirect()->back()->with('error', 'Jadwal kerja tidak ditemukan.');
        }

        // Gunakan schedule yang ditemukan
        $schedule = $activeSchedule;

        $shiftStart = Carbon::parse($schedule->workingTime->start_time)
                            ->setDateFrom($schedule->work_date);
        $shiftEnd = Carbon::parse($schedule->workingTime->end_time)
                            ->setDateFrom($schedule->work_date);

        // Shift malam (+1 hari)
        if ($schedule->workingTime->end_next_day) {
            $shiftEnd->addDay();
        }

        // Tentukan tanggal attendance berdasarkan work_date schedule
        $attendanceDate = Carbon::parse($schedule->work_date)->toDateString();

        // Ambil atau buat record attendance
        $attendance = Attendance::firstOrNew([
            'user_id' => Auth::id(),
            'date' => $attendanceDate,
        ]);
        $attendance->work_schedule_id = $schedule->id;

        // Ambil lokasi pengguna
        $userLat = $userLng = null;
        if ($request->location_lat_long) {
            [$userLat, $userLng] = explode(',', $request->location_lat_long);
        }

        // Cek lokasi radius
        $locations = Location::where('is_active', true)->get();
        $isWithinRadius = false;
        foreach ($locations as $loc) {
            if ($loc->lat_long && $userLat && $userLng) {
                [$lat, $lng] = explode(',', $loc->lat_long);
                $distance = $this->distanceInKm($userLat, $userLng, $lat, $lng);
                if ($distance <= ($loc->radius ?? 1)) {
                    $isWithinRadius = true;
                    break;
                }
            }
        }

        // Jika workingTime tidak dibatasi lokasi atau location_required != 1
        if ($locationRequired !== 1) {
            $isWithinRadius = true;
        }

        // Validasi radius jika location_required = 1
        if ($locationRequired === 1 && !$isWithinRadius) {
            return redirect()->back()->with('error', 'Anda berada di luar radius lokasi yang diperbolehkan');
        }

        // ====================
        // CLOCK IN
        // ====================
        if ($type === 'clock_in_time' && !$attendance->clock_in_time) {
            $earliestClockIn = $shiftStart->copy()->subHours(2); // 2 jam sebelum shift start

            if ($now->lessThan($earliestClockIn)) {
                return redirect()->back()->with('error', 'Clock in terlalu awal. Anda hanya bisa clock in maksimal 2 jam sebelum shift.');
            }

            $attendance->clock_in_time = $now;
            $attendance->clock_in_photo = $this->savePhoto($request->photo);
            $attendance->clock_in_lat = $userLat;
            $attendance->clock_in_lng = $userLng;
            $attendance->clock_in_notes = $isWithinRadius ? null : 'out of radius';

            $toleranceTime = $shiftStart->copy()->addMinutes($schedule->workingTime->late_tolerance_minutes ?? 0);

            if ($now->greaterThan($toleranceTime)) {
                $attendance->status = 'late';
                $attendance->late_minutes = $now->diffInMinutes($shiftStart);
            } else {
                $attendance->status = 'present';
                $attendance->late_minutes = 0;
            }
        }

        // ====================
        // CLOCK OUT
        // ====================
        if ($type === 'clock_out_time') {
            $latestClockOut = $shiftEnd->copy()->addHours(5); // 5 jam setelah shift end

            if ($now->greaterThan($latestClockOut)) {
                return redirect()->back()->with('error', 'Clock out sudah melewati batas maksimal 5 jam setelah shift berakhir.');
            }

            $attendance->clock_out_time = $now;
            $attendance->clock_out_photo = $this->savePhoto($request->photo);
            $attendance->clock_out_lat = $userLat;
            $attendance->clock_out_lng = $userLng;
            $attendance->clock_out_notes = $isWithinRadius ? null : 'out of radius';

            // Hitung jam kerja
            if ($attendance->clock_in_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $attendance->working_hours = round($clockOut->floatDiffInHours($clockIn), 2);
            }
        }

        $attendance->save();

        return redirect()->route('attendances.index')
            ->with('success', ucfirst(str_replace('_', ' ', $type)) . ' recorded');
    }
    // Simpan foto base64
    protected function savePhoto($photoData)
    {
        $folder = 'upload/attendance';
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        $photo = str_replace('data:image/jpeg;base64,', '', $photoData);
        $photo = str_replace(' ', '+', $photo);
        $fileName = uniqid('attendance_') . '.jpg';
        file_put_contents(public_path("$folder/$fileName"), base64_decode($photo));

        return "$folder/$fileName";
    }

    // Hitung jarak km
    protected function distanceInKm($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function log(Request $request)
    {
        $month = $request->month ?? date('Y-m'); 

        $data = WorkSchedule::with('attendance')
                ->with('workingTime')
                ->where('user_id', Auth::id())
                ->whereYear('work_date', explode('-', $month)[0])
                ->whereMonth('work_date', explode('-', $month)[1]) 
                ->orderBy('work_date', 'ASC')
                ->get(); 
 
        return view('attendances.log', compact('data', 'month'));

    }

}
