<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkingTime;
use Illuminate\Validation\Rule;

class WorkingTimeController extends Controller
{
    public function index()
    {
        $working_times = WorkingTime::paginate(10);
        return view('admin.master_data.working_time_list', compact('working_times'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_tolerance_minutes' => 'nullable|integer|min:0',
            'end_next_day' => 'nullable|boolean',
            'is_location_limited' => 'nullable|boolean',
            'code' => 'required|string|max:50|unique:working_times,code',
            'color' => 'nullable|string|max:15',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $workingTime = WorkingTime::create([
            'name' => $validated['name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'] ?? 0,
            'end_next_day' => $validated['end_next_day'] ?? false,
            'is_location_limited' => $validated['is_location_limited'] ?? false,
            'code' => $validated['code'],
            'color' => $validated['color'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Jam kerja berhasil ditambahkan.');
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $working_time = WorkingTime::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$working_time) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($working_time);
    }
 
    public function update(Request $request, WorkingTime $working_time)
    {

        // dd($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_tolerance_minutes' => 'nullable|integer|min:0',
            'end_next_day' => 'nullable|boolean',
            'is_location_limited' => 'nullable|boolean',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('working_times')->ignore($working_time->id),
            ],
            'color' => 'nullable|string|max:15',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $working_time->update([
            'name' => $validated['name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'] ?? 0,
            'end_next_day' => $validated['end_next_day'] ?? false,
            'is_location_limited' => $validated['is_location_limited'] ?? false,
            'code' => $validated['code'],
            'color' => $validated['color'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Jam kerja berhasil diupdate.');
    }

    public function destroy($id)
    {
        $working_time = WorkingTime::find($id);

        if (!$working_time) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $working_time->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
