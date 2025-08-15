<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->paginate(5);

        return view('admin.master_data.department_list', compact('departments'));
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 
            'code' => 'required|string|max:50|unique:departments,code',
            'is_active' => 'nullable|boolean',
        ]);

        $department =Department::create([
            'name' => $validated['name'], 
            'code' => $validated['code'],
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Position berhasil ditambahkan.');
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $department = Department::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$department) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($department);
    }
 
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'is_active' => 'nullable|boolean',
        ]);

        $department->update([
            'name' => $validated['name'], 
            'code' => $validated['code'],
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Divisi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $department->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
