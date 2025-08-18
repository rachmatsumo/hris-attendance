<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $employees = User::orderBy('name')->paginate(10);
        $positions = Position::all();
        $departments = Department::all();

        return view('admin.master_data.employee_list', compact('employees', 'departments', 'positions')); 
    }

    public function loadPosition($id)
    { 
        $positions = Position::where('department_id', $id)->get(); 

        if (!$positions) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
 
        return response()->json($positions);
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $user = User::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$user) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($user);
    }
 
    public function store(Request $request)
    {
        $default_password = setting('default_password');

        $request->validate([
            'employee_id' => 'nullable|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20', 
            'position_id' => 'required|exists:positions,id',
            'gender' => ['nullable', Rule::in(['male','female'])],
            'join_date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        // Jika employee_id kosong, ambil terakhir +1 atau buat format custom
        $employee_id = $request->employee_id;
        if (!$employee_id) {
            $lastUser = User::orderBy('id', 'desc')->first();
            $lastId = $lastUser ? intval($lastUser->employee_id) : 0;
            $employee_id = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT); // contoh format: 00001, 00002, dst
        }

        $user = User::create([
            'employee_id' => $employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, 
            'position_id' => $request->position_id,
            'gender' => $request->gender,
            'join_date' => $request->join_date,
            'is_active' => $request->is_active, 
            'password' => Hash::make($default_password),
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil ditambahkan.');
    }
 
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|unique:users,employee_id,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20', 
            'position_id' => 'required|exists:positions,id',
            'gender' => ['nullable', Rule::in(['male','female'])],
            'join_date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        // dd($request->position_id);

        $user->update([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, 
            'position_id' => $request->position_id,
            'gender' => $request->gender,
            'join_date' => $request->join_date,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    // Hapus karyawan
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus.');
    }

    
}
