<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::orderBy('grade')->paginate(10);

        return view('admin.master_data.level_list', compact('levels'));  
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|integer',
        ]);

        $Level = Level::create([
            'name' => $validated['name'],
            'grade' => $validated['grade'], 
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
    }

    public function show($id)
    { 
        $level = Level::find($id);
 
        if (!$level) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
 
        return response()->json($level);
    }
 
    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|integer',
        ]);

        $level->update([
            'name' => $validated['name'],
            'grade' => $validated['grade'], 
        ]);

        return redirect()->back()->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $level = Level::find($id);

        if (!$level) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $level->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
