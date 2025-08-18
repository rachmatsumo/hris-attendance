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
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Permohonan Cuti dan Izin</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-between align-items-center">
                            <div class="order-2 order-md-1 mb-2 col-12 col-md-4 col-lg-3">
                                <div class="input-group">
                                    <input type="number" id="year" name="year" value="{{ $year }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>    
                            </div>    
                            <div class="order-1 order-md-2 mb-2 col-12 col-md-8 col-lg-9 justify-content-end d-flex">
                                <div><span class="text-muted">Menunggu Persetujuan</span> 
                                <i class="bi bi-chevron-compact-right"></i> Cuti: <span class="badge bg-warning">{{ $leaveCount }}</span> | Izin: <span class="badge bg-warning">{{ $permitCount }}</span></div>
                            </div>
                        </div>    
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis</th>
                                    <th>ID</th>
                                    <th>Pemohon</th>
                                    <th>Jabatan</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Alasan</th>
                                    <th>Approve/Reject by</th>
                                    <th>Notes</th>
                                    <th>Dibuat</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($data as $a)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $a->type_name }}</td>
                                    <td>{{ $a?->user->employee_id }}</td>
                                    <td>{{ $a?->user->name }}</td>
                                    <td>{{ $a?->user?->position?->name }} - {{ $a?->user?->department?->name }}</td>
                                    <td>{{ $a->periode_locale }} <br> ({{ $a->total_day }} hari)</td>
                                    <td>{!! $a->status_badge !!}</td>
                                    <td>{{ $a->reason ?? '-' }}</td>
                                    <td>{{ $a->approver?->name ?? '-' }}</td>
                                    <td>{{ $a->approval_notes ?? '-' }}</td>
                                    <td>{{ $a->created_at }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-success btn-sm viewDataBtn"
                                                    data-id="{{ $a->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($a->status === 'pending')
                                                <button class="btn btn-primary btn-sm approveBtn mx-1"
                                                        data-id="{{ $a->id }}">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm rejectBtn"
                                                        data-id="{{ $a->id }}">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @else
                                                
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination flex-column justify-content-center mt-3">
                        {{ $data->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {

    // View detail
    const viewBtn = e.target.closest('.viewDataBtn');
    if(viewBtn) {
        const id = viewBtn.dataset.id;
        var img = '';
        fetch(`/attendance-permit-admin/${id}`)
        .then(res => res.json())
        .then(data => {
            let attachmentHtml = '';
            if(data.attachment !== null){
                const baseUrl = "{{ asset('') }}"; 
                const dataUrl = data.attachment;  
                attachmentHtml = `
                    <div style="margin-top: 10px;">
                        <strong>Lampiran:</strong>
                        <div style="max-height:300px; overflow:auto; margin-top:5px;">
                            <img src="${baseUrl}${dataUrl}" style="width:100%; object-fit:contain;" class="img-thumbnail">
                        </div>
                    </div>
                `;
            }

            Swal.fire({
                title: `Detail Permohonan`,
                html: `
                    <div style="text-align:left;">
                        <p><strong>Pemohon:</strong> ${data.user_info.name}</p>
                        <p><strong>Jabatan:</strong> ${data.user_info.position} - ${data.user_info.department}</p>
                        <p><strong>Jenis:</strong> ${data.type_name}</p>
                        <p><strong>Periode:</strong> ${data.periode_locale} (${data.total_day} hari)</p>
                        <p><strong>Alasan:</strong> ${data.reason ?? '-'}</p>
                        ${attachmentHtml}
                    </div>
                `,
                width: '600px',
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: 'Tutup'
            });
        });
    }

    // Approve / Reject
    const actionBtn = e.target.closest('.approveBtn, .rejectBtn');
    if(!actionBtn) return;

    const id = actionBtn.dataset.id;
    const isApprove = actionBtn.classList.contains('approveBtn');
    const url = `/attendance-permit-admin/${id}`;
    const method = isApprove ? 'PUT' : 'DELETE';

    Swal.fire({
        title: isApprove ? 'Setuju permohonan?' : 'Tolak permohonan?',
        input: 'textarea',
        inputLabel: 'Catatan / Alasan',
        inputPlaceholder: 'Ketik catatan di sini...',
        showCancelButton: true,
        confirmButtonText: isApprove ? 'Setuju' : 'Tolak',
        cancelButtonText: 'Batal',
        // preConfirm: (approval_notes) => {
        //     if(!approval_notes) Swal.showValidationMessage('Catatan harus diisi!');
        //     return approval_notes;
        // }
    }).then(result => {
        if(result.isConfirmed){
            const formData = new FormData();
            formData.append('approval_notes', result.value);
            formData.append('_token', '{{ csrf_token() }}');
            if(method === 'PUT') formData.append('_method', 'PUT');
            if(method === 'DELETE') formData.append('_method', 'DELETE');

            fetch(url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message
                }).then(() => location.reload());
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan, coba lagi.'
                });
                console.error(err);
            });
        }
    });
});
</script>
@endsection
