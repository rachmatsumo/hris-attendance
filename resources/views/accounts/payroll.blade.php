@extends('layouts.app')

@section('content') 
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Header & Filter -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('account.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Payroll Statement</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-between align-items-center">
                            <div class="order-2 order-md-1 mb-2 col-12 col-md-4 col-lg-3">
                                <div class="input-group">
                                    <input type="number" id="year" name="year" value="{{ $year }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>    
                            </div>     
                        </div>    
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Gross</th>
                                    <th>Deduction</th>
                                    <th>THP</th>
                                    <th>Jenis Payroll</th>
                                    <th>Dibuat</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($payrolls as $a)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $a->period }}</td>
                                    <td>{{ optional($a)->incomes_total_formatted  }}</td>
                                    <td>{{ optional($a)->deductions_total_formatted  }}</td>
                                    <td>{{ optional($a)->net_salary_formatted  }}</td>
                                    <td>{{ ucwords($a->payroll_type) }}</td> 
                                    <td>{{ $a->created_at }}</td> 
                                    <td>
                                        <div class="d-flex">
                                            <a class="btn btn-danger btn-sm viewDataBtn me-2"
                                                    href="{{ route('payroll-admin.download-pdf', $a->id) }}">
                                                <i class="bi bi-file-pdf"></i>
                                            </a> 
                                            <a class="btn btn-success btn-sm viewDataBtn me-2"
                                                    href="{{ route('payroll-admin.show', $a->id) }}">
                                                <i class="bi bi-eye"></i>
                                            </a>  
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> 

                    <div class="pagination flex-column justify-content-center mt-3">
                        {{ $payrolls->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection
