<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendancePermit;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendancePermitController extends Controller
{
    public function index(Request $request)
    {   
        $year = $request->input('year') ?? date('Y');

        $user = Auth::user();
        $leavePermits = $user->leaveQuota(); 

        $data = AttendancePermit::with('approver')
                                ->where('user_id', $user->id)
                                ->whereYear('start_date', $year)
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        // dd($data);

        return view('attendance_permits.attendance_permits', compact('data', 'leavePermits', 'year'));
    }

    public function quotaCheck(Request $request)
    {
        $type = $request->query('type'); // leave, late_arrival, etc
        $year = $request->query('year') ?? date('Y');

        $user = Auth::user();
        $quota = $user->leaveQuota(); 

        $sisa = $type === 'leave' 
            ? $quota['sisa_cuti'] 
            : $quota['sisa_izin'];


        return response()->json([
            'sisa' => $sisa,
            'limit' => $type === 'leave' ? $quota['limit_cuti'] : $quota['limit_izin'],
            'used'  => $type === 'leave' ? $quota['count_cuti'] : $quota['count_izin'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'periode' => 'required|string|max:100',
            'reason' => 'nullable|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        // ambil user aktif
        $user = auth()->user();

        // default evidence null
        $evidencePath = null;

        // cek apakah ada file yang diupload
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/leave'), $filename);

            $evidencePath = 'upload/leave/' . $filename;
        } 

        if (strpos($validated['periode'], ' - ') !== false) { 
            [$start_date, $end_date] = explode(' - ', $validated['periode']);
        } else { 
            $start_date = $validated['periode'];
            $end_date   = $validated['periode'];
        } 

        $start = Carbon::parse($start_date);
        $end   = Carbon::parse($end_date);
 
        $totalDay = $start->diffInDays($end) + 1;

        $leave = AttendancePermit::create([
            'user_id'    => $user->id,
            'type'       => $validated['type'],
            'start_date' => $start->format('Y-m-d'),
            'end_date'   => $end->format('Y-m-d'),
            'total_day'  => $totalDay,
            'reason'     => $validated['reason'] ?? null,
            'attachment'   => $evidencePath,
            'status'     => 'pending'
        ]);

        return redirect()->back()->with('success', 'Permohonan berhasil dikirim.');
    }

    public function show($id)
    { 
        $data = AttendancePermit::find($id);
 
        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
 
        return response()->json($data);
    }

    public function destroy($id)
    {
        $AttendancePermit = AttendancePermit::find($id);

        if (!$AttendancePermit) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $AttendancePermit->update([
            'status' => 'withdraw'
        ]);

        return redirect()->back()->with('success', 'Permohonan dibatalkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'periode' => 'required|string|max:100',
            'reason' => 'nullable|string|max:255',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        $AttendancePermit = AttendancePermit::find($id);

        if (!$AttendancePermit) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // handle evidence
        $evidencePath = $AttendancePermit->evidence; // default tetap yang lama

        if ($request->hasFile('evidence')) {
            // hapus file lama kalau ada
            if ($evidencePath && file_exists(public_path($evidencePath))) {
                unlink(public_path($evidencePath));
            }

            $file = $request->file('evidence');
            $filename = time() . '_' . auth()->id() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/leave'), $filename);

            $evidencePath = 'upload/leave/' . $filename;
        }

        // parsing periode
        if (strpos($validated['periode'], ' - ') !== false) { 
            [$start_date, $end_date] = explode(' - ', $validated['periode']);
        } else { 
            $start_date = $validated['periode'];
            $end_date   = $validated['periode'];
        } 

        $start = Carbon::parse($start_date);
        $end   = Carbon::parse($end_date);
        $totalDay = $start->diffInDays($end) + 1;

        $AttendancePermit->update([
            'type'       => $validated['type'],
            'start_date' => $start->format('Y-m-d'),
            'end_date'   => $end->format('Y-m-d'),
            'total_day'  => $totalDay,
            'reason'     => $validated['reason'] ?? null,
            'attachment'   => $evidencePath,
            // status tetap biarkan apa adanya, jangan diubah ke pending lagi
        ]);

        return redirect()->back()->with('success', 'Permohonan berhasil diperbarui.');
    }


}
