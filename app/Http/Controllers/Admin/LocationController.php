<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $work_locations = Location::orderBy('name')->paginate(10);

        return view('admin.master_data.work_location', compact('work_locations'));  
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat_long' => 'required|string|max:255',
            'radius' => 'required|integer|max:255', 
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];

        $location = Location::create([
            'name' => $validated['name'],
            'lat_long' => $validated['lat_long'],
            'radius' => $validated['radius'], 
            'is_active' => (int) $isActive, 
        ]);

        return redirect()->back()->with('success', 'Area kerja berhasil ditambahkan.');
    }

    public function show($id)
    {
        // Ambil data berdasarkan ID
        $location = Location::find($id);

        // Jika data tidak ditemukan, kembalikan error 404 JSON
        if (!$location) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Kembalikan data dalam format JSON
        return response()->json($location);
    }
 
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat_long' => 'required|string|max:255',
            'radius' => 'required|integer|max:255', 
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $validated['is_active'];


        $location->update([
            'name' => $validated['name'],
            'lat_long' => $validated['lat_long'],
            'radius' => $validated['radius'], 
            'is_active' => (int) $isActive, 
        ]);

        // dd($location); // Debugging line, can be removed later


        return redirect()->back()->with('success', 'Area kerja berhasil diupdate.');
    }

    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $location->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
