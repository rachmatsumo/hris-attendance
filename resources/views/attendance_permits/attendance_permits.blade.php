@extends('layouts.app')

@section('content') 
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-end align-items-center">
                        {{-- <a href="{{ route('admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a> --}}
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('attendance-permit.store') }}" title="Buat Permohonan Cuti/Izin" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Cuti dan Izin</h6>
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
                                <div><span class="text-muted">Tersedia</span> <i class="bi bi-chevron-compact-right"></i> Cuti : <span class="badge bg-success">{{ $leavePermits['sisa_cuti'] }}</span> | Izin : <span class="badge bg-success">{{ $leavePermits['sisa_izin'] }}</span></div>
                            </div>
                        </div>    
                    </form>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr> 
                                    <th class="v-middle">No</th>
                                    <th class="v-middle">Jenis</th>
                                    <th class="v-middle">Periode</th> 
                                    <th class="v-middle">Status</th>
                                    <th class="v-middle">Alasan</th>
                                    <th class="v-middle">Approve/Reject by</th>
                                    <th class="v-middle">Notes</th>
                                    <th class="v-middle">Dibuat</th>
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1 ; @endphp
                                @forelse($data as $a)
                                <tr>
                                    <td class="v-middle">{{ $no++ }}</td>
                                    <td class="v-middle">{{ $a->type_name }}</td>
                                    <td class="v-middle">{{ $a->periode_locale }} <br> ({{ $a->total_day }} hari)</td> 
                                    <td class="v-middle">{!! $a->status_badge !!}</td>
                                    <td class="v-middle">{{ $a->reason ?? '-' }}</td>
                                    <td class="v-middle">{{ $a->approver?->name ?? '-' }}</td>
                                    <td class="v-middle">{{ $a->approval_notes ?? '-' }}</td>
                                    <td class="v-middle">{{ $a->created_at }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if($a->status === 'pending')
                                            <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInput"
                                                    method="put"
                                                    title="Edit Permohonan Cuti/Izin"
                                                    data-id="{{ $a->id }}"
                                                    data-url="{{ route('attendance-permit.update', $a->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('attendance-permit.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin untuk menarik permohonan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash2"></i></button>
                                            </form>
                                            @else
                                            -
                                            @endif
                                        </div>
                                    </td> 
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data</td>
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

<!-- Modal -->
<div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="" method="POST" id="inputForm" enctype="multipart/form-data">
        <!-- CSRF token jika Laravel -->
        @csrf
        <div id="methodField"></div>
        <div class="modal-header">
          <h5 class="modal-title" id="modalInputLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

            <div class="mb-3">
                <label for="type" class="form-label">Jenis Permohonan</label>
                <select class="form-select" id="type" name="type" onChange="changeForm();" required>
                    <option value="">::Pilih Jenis Cuti / Izin::</option>
                    <option value="late_arrival">Terlambat Masuk</option>
                    <option value="early_departure">Pulang Awal</option>
                    <option value="sick_during_work">Sakit Saat Kerja</option>
                    <option value="urgent_leave">Izin Mendesak</option>
                    <option value="leave">Cuti</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="periode" class="form-label">Periode</label>
                <input type="text" class="form-control" id="periode" name="periode" placeholder="Pilih tanggal mulai & selesai" onChange="changeForm();" required>
            </div>

            <div class="mb-3">
                <label for="total_days" class="form-label">Total Hari</label>
                <input type="number" class="form-control" id="total_days" name="total_days" readonly>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Alasan / Catatan</label>
                <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="evidence" class="form-label">Bukti (Opsional)</label>
                <input type="file" class="form-control" id="evidence" name="evidence" accept="image/*">
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="submit" disabled="true">Kirim Permohonan <i class="bi bi-arrow-right"></i></button>
        </div>
      </form>
    </div>
  </div>
</div> 

<script>
    let periodePicker; // simpan global

    document.addEventListener("DOMContentLoaded", function() {
        periodePicker = flatpickr("#periode", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "id",
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    let start = selectedDates[0];
                    let end = selectedDates[1];
                    let diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                    document.getElementById("total_days").value = diff;
                }
            }
        });
    });

    function changeForm(){
        let type = document.getElementById("type").value;
        let totalDays = document.getElementById("total_days").value;
        let periode = document.getElementById("periode").value;

        let dates = periode.split(" to ");
        let startDate = new Date(dates[0]);
        let year = startDate.getFullYear();

        fetch(`{{ route('attendance-permit.quota-check') }}?type=${type}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                if (data.sisa <= 0) {
                    alert("Kuota " + (type === 'leave' ? 'Cuti' : 'Izin') + " sudah habis di tahun " + year + "!");
                    this.value = "";
                }
                if(data.sisa < totalDays){
                    $('#submit').attr('disabled', true);
                }else{
                    if(periode){
                        $('#submit').attr('disabled', false);
                    }
                }
            }); 
    }
    
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.editDataBtn');
        if(btn) {
            const id = btn.dataset.id;
            const url = `/attendance-permit/${id}`;
            const form = document.getElementById('inputForm');
            const methodField = document.getElementById('methodField');

            fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log(data);

                form.action = url;
                methodField.innerHTML = '@method("PUT")';

                form.querySelector('#type').value = data.type;
                periodePicker.setDate([data.start_date, data.end_date], true);
                form.querySelector('#total_days').value = data.total_day; 
                form.querySelector('#reason').value = data.reason; 
            })
            .catch(err => console.error(err));
        }
    });
</script>

@endsection