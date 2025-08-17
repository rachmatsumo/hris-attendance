<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Department;
use App\Models\Level;

class PositionController extends Controller
{
    public function index()
    { 
        $positions = Position::with('department')
            ->withSum(['incomes as bruto' => function($query) {
                $query->where('is_active', 1)
                    ->where('category', 'base');
            }], 'value') // sum kolom 'value' dari incomes
            ->paginate(10);

        $departments = Department::orderBy('name', 'asc')->get();
        $levels = Level::orderBy('grade', 'asc')->get();

        return view('admin.master_data.position_list', compact('positions', 'departments', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            // 'code' => 'required|string|max:50|unique:positions,code',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $position = Position::create([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'level_id' => $validated['level_id'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Position berhasil ditambahkan.');
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $position = Position::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$position) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($position);
    }
 
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            // 'code' => 'required|string|max:50|unique:positions,code,' . $position->id,
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $position->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'level_id' => $validated['level_id'],
            'is_active' => (int) $isActive,
        ]);

        return redirect()->back()->with('success', 'Position berhasil diupdate.');
    }

    public function destroy($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $position->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
