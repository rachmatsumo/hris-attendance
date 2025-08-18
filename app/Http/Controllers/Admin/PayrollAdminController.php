<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon; 
use Mpdf\Mpdf;
use Illuminate\Support\Str;
use App\Exports\MonthlyPayrollExport;
use Maatwebsite\Excel\Facades\Excel;

class PayrollAdminController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month') ?? date('Y-m');
        $carbon = Carbon::createFromFormat('Y-m', $month);
        $users = User::with('position')->where('is_active', 1)->get(); 
        
        $year_select  = $carbon->year;    
        $month_select = $carbon->month;  

        $payrolls = Payroll::with('user')
                            ->where('month', $month_select)
                            ->where('year', $year_select)  
                            ->paginate(10); 
                            
        $payrolls = $payrolls->appends([ 
            'month' => $month
        ]);

        return view('admin.resource_management.payroll', compact('month', 'payrolls', 'users'));
 
    }

    // For generating payroll
    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id'
        ]);

        $carbon = Carbon::createFromFormat('Y-m', $validated['month']); 
            
        $year_select  = $carbon->year;    
        $month_select = $carbon->month;  

        $userData = User::whereIn('id', $validated['users'])->get();
        
        $results = [
            'success' => [],
            'failed'  => []
        ];

        foreach ($userData as $user) {
            try {
                $payroll = Payroll::generateRegularPayroll($user, $month_select, $year_select);

                $results['success'][] = [
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'status'  => $payroll->wasRecentlyCreated ? 'created' : 'updated',
                ];

            } catch (\Exception $e) { 
                $results['failed'][] = [
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        return redirect()->back()->with([
            'success' => 'Batch payroll selesai diproses.',
            'results' => $results
        ]);
    }

    // For set Paid
    public function setPaid(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $carbon = Carbon::createFromFormat('Y-m', $validated['month']);

        $year_select  = $carbon->year;    
        $month_select = $carbon->month;  

        // Update payroll
        $updated = Payroll::where('year', $year_select)
            ->where('month', $month_select)
            ->update([
                'status'  => 'paid',
                'paid_at' => now(),
                'updated_at' => now()
            ]);
        
        return redirect()->back()->with('success', "Payroll bulan {$validated['month']} berhasil diset Paid untuk {$updated} karyawan.");
    }

    public function show($id)
    {
        $payroll = Payroll::with('user')->where('id', $id)->first();  
        // dd($payroll);
        if (!$payroll) {
            return redirect()->back()->with('error', "Payroll tidak ditemukan.");
        }
 
        return view('payroll.statement', compact('payroll'));
    }

    public function destroy($id)
    {
        $payroll = Payroll::find($id);

        if (!$payroll) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $payroll->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function downloadPdf($id)
    {
        $payroll = Payroll::with('user')->findOrFail($id);
        $html = view('payroll.statement_pdf', compact('payroll'))->render(); 
        $mpdf = new Mpdf(['default_font' => 'dejavusans']);
        $mpdf->WriteHTML($html);
 
        $monthYear = Carbon::parse($payroll->created_at)->format('F-Y');
        $timestamp = now()->timestamp;
        $filename = 'Slip-' . Str::slug($payroll->user->name) . "-{$monthYear}-{$timestamp}.pdf";

        return $mpdf->Output($filename, 'I'); // 'I' = inline di browser, 'D' = download langsung
    }

    public function exportMonthly(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m'); 
        $timestamp = now()->format('dHis'); 

        $name = $month . '__' . $timestamp;
        return Excel::download(new  MonthlyPayrollExport($month), "Payroll_$name.xlsx");
    }
}
