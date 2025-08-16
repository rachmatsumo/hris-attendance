<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        return view('admin.master_data.settings', compact('settings'));
    } 
    
    public function show($id)
    {
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $type = $setting->type; // ambil tipe dari kolom type
        $value = $setting->value;
        $description = $setting->description;

        // Generate HTML untuk value
        switch ($type) {
            case 'boolean':
                $element = '<select class="form-select" name="value">';
                $element .= '<option value="0"'.($value == 0 ? ' selected' : '').'>Tidak Aktif</option>';
                $element .= '<option value="1"'.($value == 1 ? ' selected' : '').'>Aktif</option>';
                $element .= '</select>';
                break;

            case 'integer':
                $element = '<input type="number" class="form-control" name="value" value="'.$value.'">';
                break;

            case 'string':
            default:
                $element = '<input type="text" class="form-control" name="value" value="'.$value.'">';
                break;
        }

        return response()->json([
            'element' => $element,
            'description' => $description
        ]);
    } 

    public function update(Request $request, Setting $setting)
    { 
        $type = $setting->type; 
        $rules = [];

        switch ($type) {
            case 'integer':
                $rules['value'] = 'required|integer';
                break;
            case 'boolean':
                $rules['value'] = 'required|in:0,1';
                break;
            case 'string':
            default:
                $rules['value'] = 'required|string|max:255';
                break;
        }
 
        $validated = $request->validate($rules);
 
        $setting->update([
            'value' => $validated['value']
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil diupdate.');
    }
}
