@extends('layouts.app')

@section('content') 
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a> 
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('working-time.store') }}" title="Tambah Jam Kerja" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Jam Kerja</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">Jam Kerja</th>
                                    <th class="v-middle">Kode</th>
                                    <th class="v-middle">Masuk</th>
                                    <th class="v-middle">Pulang</th>
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($working_times as $a)
                                <tr>
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td class="v-middle">{{ $a->code }}</td>
                                    <td class="v-middle">{{ $a->start_time }}</td>
                                    <td class="v-middle">{{ $a->end_time }}</td> 
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInput"
                                                    method="put"
                                                    title="Edit Jam Kerja"
                                                    data-id="{{ $a->id }}"
                                                    data-url="{{ route('working-time.update', $a->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('working-time.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash2"></i></button>
                                            </form>
                                        </div>
                                    </td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination flex-column justify-content-center mt-3"> 
                        {{ $working_times->links('pagination::bootstrap-5') }}
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
                <label for="name" class="form-label">Nama Jam Kerja</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3 row">

                <div class="col-8 col-md-10 mb-3"> 
                   <label for="code" class="form-label">Kode Jam Kerja</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div> 
                    
                <div class="col-4 col-md-2 mb-3">
                    <label for="color" class="form-label">Warna</label>
                    <input type="color" class="form-control" id="color" name="color" value="#fff">
                </div> 
            
            </div>  
            
            <div class="mb-3 row">
                <div class="mb-3 col-6 col-md-6">
                    <label for="start_time" class="form-label">Masuk</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>
                
                <div class="col-6 col-md-66 mb-3">
                    <label for="end_time" class="form-label">Pulang</label>
                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>
            </div>
                
            <div class="mb-3 row">
                <div class="col-6 col-md-6 mb-3">
                    <label for="late_tolerance_minutes" class="form-label">Toleransi Terlambat</label>
                    <input type="number" class="form-control" id="late_tolerance_minutes" name="late_tolerance_minutes" value="15" required>
                </div> 
                
                <div class="col-6 col-md-6 mb-3"> 
                    <label class="form-label" for="end_next_day">+1 Hari (Overnight Shift)</label>
                    <select class="form-select" id="end_next_day" name="end_next_day">
                        <option value="0">Tidak</option>
                        <option value="1">+1 Hari</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">

                <div class="col-12 col-md-12 mb-3"> 
                    <label class="form-label" for="is_location_limited">Lokasi</label>
                    <select class="form-select" id="is_location_limited" name="is_location_limited">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div> 
                    
                {{-- <div class="col-6 col-md-6 mb-3">
                    <label for="color" class="form-label">Warna</label>
                    <input type="color" class="form-control" id="color" name="color" value="#fff">
                </div>  --}}
            
            </div> 

            <div class="mb-3"> 
                <label class="form-label" for="is_active">Status</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.editDataBtn');
        if(btn) {
            const id = btn.dataset.id;
            const url = `/working-time/${id}`; // route show bisa dikustom
            const form = document.getElementById('inputForm');
            const methodField = document.getElementById('methodField');

            fetch(url)
            .then(res => res.json())
            .then(data => {
              console.log(data);
              // Set action form dan method
                    form.action = url;
                    methodField.innerHTML = '@method("PUT")';

                    // Isi field
                    form.querySelector('#name').value = data.name;
                    form.querySelector('#start_time').value = data.start_time;
                    form.querySelector('#end_time').value = data.end_time;
                    form.querySelector('#late_tolerance_minutes').value = data.late_tolerance_minutes;
                    form.querySelector('#end_next_day').value = data.end_next_day;
                    form.querySelector('#is_location_limited').value = data.is_location_limited;
                    form.querySelector('#code').value = data.code; 
                    form.querySelector('#color').value = data.color; 
                    form.querySelector('#is_active').value = data.is_active;

                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection