<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('name')->paginate(10);

        return view('admin.master_data.holiday_list', compact('holidays'));  
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|max:20', 
            'date' => 'required|date_format:Y-m-d',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $holiday = Holiday::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'date' => $validated['date'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $holiday = Holiday::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$holiday) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($holiday);
    }
 
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|max:20', 
            'date' => 'required|date_format:Y-m-d',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];


        $holiday->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'date' => $validated['date'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Hari libur berhasil diupdate.');
    }

    public function destroy($id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $holiday->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
