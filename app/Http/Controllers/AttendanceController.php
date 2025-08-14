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
            ->orderBy('date', 'desc')
            ->limit(15)
            ->paginate(5);

        $schedule = WorkSchedule::where('user_id', Auth::id())
            ->where('day_of_week', now()->dayOfWeek)
            ->where('is_active', true)
            ->first();

        $locations = Location::where('is_active', true)->get();

            // dd($attendances);

        return view('attendances.attendance', compact('attendances', 'schedule', 'locations'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type'); // clock_in_time atau clock_out_time

        // Validasi wajib foto
        if (!$request->photo) {
            return redirect()->back()->with('error', 'Foto wajib diambil sebelum clock in/out');
        }

        // Cari attendance hari ini
        $attendance = Attendance::firstOrNew([
            'user_id' => Auth::id(),
            'date' => now()->toDateString(),
        ]);

        // Status dan late
        $schedule = WorkSchedule::where('user_id', Auth::id())
            ->where('day_of_week', now()->dayOfWeek)
            ->where('is_active', true)
            ->first();

            // Ambil lokasi pengguna
        $userLat = $userLng = null;
        if ($request->location_lat_long) {
            [$userLat, $userLng] = explode(',', $request->location_lat_long);
        }

        // Ambil lokasi aktif schedule jika ada
        $locations = Location::where('is_active', true)->get();

        // Fungsi helper untuk cek radius
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

        // dd($schedule, $isWithinRadius, $userLat, $userLng);

        if($schedule?->is_location_limited === 0){
            $isWithinRadius = true; // Jika lokasi tidak dibatasi, set ke true
        }

        if ($type === 'clock_in_time' && !$attendance->clock_in_time) {
            $attendance->clock_in_time = now();
            $attendance->clock_in_photo = $this->savePhoto($request->photo);
            $attendance->clock_in_lat = $userLat;
            $attendance->clock_in_lng = $userLng;
            $attendance->clock_in_notes = $isWithinRadius ? null : 'out of radius';

            if ($schedule) {
                $scheduledStart = Carbon::parse($schedule->start_time);
                $toleranceTime = $scheduledStart->copy()->addMinutes($schedule->late_tolerance_minutes ?? 0);
                if (Carbon::parse($attendance->clock_in_time)->greaterThan($toleranceTime)) {
                    $attendance->status = 'late';
                    $attendance->late_minutes = Carbon::parse($attendance->clock_in_time)->diffInMinutes($scheduledStart);
                } else {
                    $attendance->status = 'present';
                    $attendance->late_minutes = 0;
                }
            } else {
                $attendance->status = 'overtime';
                $attendance->late_minutes = 0;
            }
        }

        if ($type === 'clock_out_time') {
            $attendance->clock_out_time = now();
            $attendance->clock_out_photo = $this->savePhoto($request->photo);
            $attendance->clock_out_lat = $userLat;
            $attendance->clock_out_lng = $userLng;
            $attendance->clock_out_notes = $isWithinRadius ? null : 'out of radius';

            // Hitung jam kerja
            if ($attendance->clock_in_time) {
                $attendance->working_hours = Carbon::parse($attendance->clock_out_time)
                    ->diffInHours(Carbon::parse($attendance->clock_in_time));
            }
        }

        $attendance->save();

        return redirect()->route('attendances.index')
            ->with('success', ucfirst(str_replace('_', ' ', $type)) . ' recorded');
    }

    // Fungsi simpan foto base64 ke file
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

    protected function distanceInKm($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }




}
