<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Level;

class IncomeController extends Controller
{
    // Tampilkan modal incomes
    public function modal(Level $level)
    {
        $incomes = Income::where('level_id', $level->id)->get();
        return view('admin.master_data.modal_incomes', compact('level', 'incomes'));
    }

    // Simpan incomes bulk
    public function store(Request $request, Level $level)
    {
        $request->validate([
            'name.*' => 'required|string|max:255',
            'category.*' => 'required|string|max:255',
            'value.*' => 'required|numeric',
            'is_active.*' => 'sometimes|boolean'
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
                'category' => $request->category[$index],
                'value' => $request->value[$index],
                'is_active' => $isActive,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Hapus data lama
        Income::where('level_id', $level->id)->delete();
        Income::insert($data);

        return response()->json(['success' => true]);
    }
}
