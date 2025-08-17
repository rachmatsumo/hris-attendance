<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deduction;
use App\Models\Level;

class DeductionController extends Controller
{
    // Tampilkan modal deductions
    public function modal(Level $level)
    {
        $deductions = Deduction::where('level_id', $level->id)->get();
        return view('admin.master_data.modal_deductions', compact('level', 'deductions'));
    }

    // Simpan deductions bulk
    public function store(Request $request, Level $level)
    {
        $request->validate([
            'name.*' => 'required|string|max:255',
            'value.*' => 'required|numeric',
            'type_value.*' => 'required|string',
            'is_active.*' => 'sometimes|integer'
        ]);

        $data = [];
        foreach ($request->name as $index => $name) {
            $isActive = 0; // Default false
            if (isset($request->is_active[$index])) {
                $value = $request->is_active[$index];
                $isActive = in_array($value, [1, '1', 'true', 'on', true]) ? 1 : 0;
            }

            $data[] = [
                'level_id' => $level->id,
                'name' => $name,
                'value' => $request->value[$index],
                'type_value' => $request->type_value[$index],
                'is_active' => $isActive,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Deduction::where('level_id', $level->id)->delete();
        Deduction::insert($data);

        return response()->json(['success' => true]);
    }
}