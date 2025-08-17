<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendancePermit;
use Illuminate\Support\Facades\Auth;

class AttendancePermitAdminController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year') ?? date('Y');

        $leaveCount = AttendancePermit::where('type', 'leave')
            ->where('status', 'pending')
            ->whereYear('start_date', $year)
            ->count(); 

        $permitCount = AttendancePermit::where('type', '!=', 'leave')
            ->where('status', 'pending')
            ->whereYear('start_date', $year)
            ->count();

        $data = AttendancePermit::with('approver') 
                                ->with('user')
                                ->whereYear('start_date', $year)
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('admin.resource_management.attendance_permit', compact('data', 'year', 'leaveCount', 'permitCount'));
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
 
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:255',  
        ]);

        $permit = AttendancePermit::findOrFail($id);

        $permit->update([
            'approval_notes' => $validated['approval_notes'], 
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json(['message' => 'Permohonan disetujui']);
    }

    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'approval_notes' => 'required|string|max:255',
        ]);

        $permit = AttendancePermit::findOrFail($id);

        $permit->update([
            'status' => 'rejected',
            'approval_notes' => $validated['approval_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json(['message' => 'Permohonan ditolak']);
    }
}
