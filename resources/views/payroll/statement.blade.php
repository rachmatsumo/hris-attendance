@extends('layouts.app')

@section('content') 
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('payroll-admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <a class="btn btn-danger" href="{{ route('payroll-admin.download-pdf', $payroll->id) }}" title="Download pdf"><i class="bi bi-file-pdf"></i></a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Payroll Statement</h6>
                        <div class="d-flex flex-column align-items-end">
                            <h5>{{ setting('company_name') }}</h5>
                            <span>{{ setting('company_address') }}</span>
                        </div>
                    </div>

                    <div class="row mt-3 mb-4 pb-4 border-bottom">

                        <div class="col-12 col-md-12 bg-secondary py-2 mb-2">
                            <h4 class="text-white text-center mb-0"> Informasi Karyawan</h4>
                        </div>
                        
                        <div class="col-12 col-md-6 mb-2"> 
                            <table class="w-100"> 
                                <tr>
                                    <td class="text-start">ID Karyawan</td>
                                    <td class="text-end">{{ $payroll->user->employee_id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Nama Karyawan</td>
                                    <td class="text-end">{{ $payroll->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Jabatan</td>
                                    <td class="text-end">{{ $payroll->user->position->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Divisi</td>
                                    <td class="text-end">{{ $payroll->user->department->name }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-12 col-md-6 mb-2"> 
                            <table class="w-100">
                                <tr>
                                    <td class="text-start">Periode</td>
                                    <td class="text-end">{{ $payroll->period }}</td>
                                </tr> 
                                <tr>
                                    <td class="text-start">Payroll Level</td>
                                    <td class="text-end">{{ $payroll->user->position->level->name }} ({{ $payroll->user->position->level->grade }})</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Payroll Category</td>
                                    <td class="text-end">{{ ucwords($payroll->payroll_type) }}</td>
                                </tr> 
                            </table>
                        </div>
                    
                    </div>

                    <div class="row mb-2"> 

                        <div class="col-12 col-md-12 bg-secondary py-2 mb-2">
                            <h4 class="text-white text-center mb-0"> Penghasilan</h4>
                        </div>
                     
                        @php
                            $incomes = json_decode($payroll->incomes_data); 
                            $deductions = json_decode($payroll->deductions_data); 
                        @endphp

                        <div class="col-12 col-md-6 mb-2">
                            <h6>Incomes</h6>
                            <table class="w-100">
                                @foreach($incomes as $a)
                                    <tr>
                                        <td class="text-start">{{ $a->name }}</td>
                                        <td class="text-end">{{ number_format(optional($a)->value) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-start">Total Incomes</td>
                                    <td class="text-end">{{ number_format($payroll->incomes_total) }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-12 col-md-6 mb-2">
                            <h6>Deductions</h6> 
                            <table class="w-100">
                                @foreach($deductions as $a)
                                    <tr>
                                        <td class="text-start">{{ $a->name }}</td>
                                        <td class="text-end">{{ number_format(optional($a)->value) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-start">Total Deductions</td>
                                    <td class="text-end">{{ number_format($payroll->deductions_total) }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-12 col-md-12 bg-secondary py-2 mb-2">
                            <h4 class="text-white text-center mb-0"> Total</h4>
                        </div>

                        <div class="col-12 col-md-12 py-4 border-top border-bottom">
                            <h6>Total Take Home Pay</h6>
                            <h5>{{ number_format($payroll->net_salary) }}</h5>
                        </div>

                    </div>

                    <div class="row justify-content-end">
                        <div class="col-12 text-end">
                            {{ $payroll->created_at->translatedFormat('d F Y')}}, {{ setting('company_address') }}
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div> 

@endsection