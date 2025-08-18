@extends('layouts.app')

@section('content') 
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Header & Filter -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div class="d-flex">
                            {{-- <a class="btn btn-danger me-2" href=""><i class="bi bi-trash"></i></a> --}}
                            <a class="btn btn-warning me-2" href="#" id="btnPaidAll">Paid All</a>
                            <form id="paidAllForm" action="{{ route('payroll-admin.set-paid') }}" method="POST" style="display:none;">
                                @csrf
                                <input type="hidden" name="month" value="{{ $month }}">
                            </form>


                            <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('payroll-admin.store') }}" title="Buat Payroll" data-id=""><i class="bi bi-plus"></i></a>               
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6 class="mb-0">Payroll Admin</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-between align-items-center">
                            <div class="mb-2 col-10 col-md-4 col-lg-3">
                                <div class="input-group">
                                    <input type="month" id="month" name="month" value="{{ $month }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>    
                            </div>    
                            
                            <div class="mb-2 col-2 col-md-8 col-lg-9 justify-content-end d-flex">
                                <a href="{{ route('payroll-admin.export', ['month'=>$month]) }}" class="btn btn-success"><i class="bi bi-file-earmark-spreadsheet"></i></a>
                            </div> 
                        </div>    
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Gross</th>
                                    <th>Deduction</th>
                                    <th>THP</th>
                                    <th>Jenis Payroll</th>
                                    <th>Status</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($payrolls as $a)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $a->user?->employee_id }}</td>
                                    <td>{{ $a->user?->name }}</td>
                                    <td>{{ $a->user?->position?->name }} - {{ $a->user?->department?->name }}</td>
                                    <td>{{ optional($a)->incomes_total_formatted  }}</td>
                                    <td>{{ optional($a)->deductions_total_formatted  }}</td>
                                    <td>{{ optional($a)->net_salary_formatted  }}</td>
                                    <td>{{ ucwords($a->payroll_type) }}</td> 
                                    <td>{!! $a->status_badge !!}</td> 
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
                                            @if($a->status !== 'paid')
                                                <form action="{{ route('payroll-admin.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash2"></i></button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
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
 

<!-- Modal -->
<div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="" method="POST" id="inputForm">
        <!-- CSRF token jika Laravel -->
        @csrf
        <div id="methodField"></div>
        <div class="modal-header">
          <h5 class="modal-title" id="modalInputLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">Payroll Periode</label>
                <input type="month" class="form-control" id="month" name="month" required>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Pilih Karyawan</label>
                <select name="users[]" class="form-control" multiple required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                <small>(Ctrl/Cmd untuk pilih lebih dari 1)</small>
            </div> 
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Generate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('btnPaidAll').addEventListener('click', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menandai semua payroll bulan ini sebagai Paid?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, set Paid!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('paidAllForm').submit();
        }
    });
});
</script>
@endsection
