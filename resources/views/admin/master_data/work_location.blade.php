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
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('location.store') }}" title="Tambah Area Kerja" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Area Kerja</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">Nama Lokasi</th>
                                    <th class="v-middle">Koordinat</th>
                                    <th class="v-middle">Radius</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($work_locations as $a)
                                <tr>
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td class="v-middle">{{ $a->lat_long }}</td>
                                    <td class="v-middle">{{ $a->radius }}km</td> 
                                    <td>
                                          <button class="btn btn-sm btn-primary openModalInputBtn editDataBtn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInput"
                                                method="put"
                                                title="Edit Area Kerja"
                                                data-id="{{ $a->id }}"
                                                data-url="{{ route('location.update', $a->id) }}">
                                            Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('location.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3"> 
                        {{ $work_locations->links('pagination::bootstrap-5') }}
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
            <label for="name" class="form-label">Nama Lokasi</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="mb-3">
            <label for="lat_long" class="form-label">Latitude, Longitude</label>
            <input type="text" class="form-control" id="lat_long" name="lat_long" placeholder="-6.200000,106.816666" required>
          </div>

          <div class="mb-3">
            <label for="radius" class="form-label">Radius (km)</label>
            <input type="number" class="form-control" id="radius" name="radius" required>
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
        if(e.target && e.target.classList.contains('editDataBtn')) {
            const id = e.target.dataset.id;
            const url = `/location/${id}`; // route show bisa dikustom
            const form = document.getElementById('inputForm');
            const methodField = document.getElementById('methodField');

            
            fetch(url)
            .then(res => res.json())
            .then(data => {
              console.log(data);
              // Set action form dan method
                    form.action = e.target.dataset.url;
                    methodField.innerHTML = '@method("PUT")';

                    // Isi field
                    form.querySelector('#name').value = data.name;
                    form.querySelector('#lat_long').value = data.lat_long;
                    form.querySelector('#radius').value = data.radius; 
                    form.querySelector('#is_active').value = data.is_active;

                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection