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
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('department.store') }}" title="Tambah Divisi" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Daftar Divisi</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">Nama Divisi</th>
                                    <th class="v-middle">Code</th>
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $a)
                                <tr>
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td class="v-middle">{{ $a->code }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInput"
                                                    method="put"
                                                    title="Edit Divisi"
                                                    data-id="{{ $a->id }}"
                                                    data-url="{{ route('department.update', $a->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('department.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
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
                        {{ $departments->links('pagination::bootstrap-5') }}
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
                <label for="name" class="form-label">Nama Divisi</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Kode Divisi</label>
                <input type="text" class="form-control" id="code" name="code" required>
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
            const url = `/department/${id}`; // route show bisa dikustom
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
                    form.querySelector('#code').value = data.code; 
                    form.querySelector('#is_active').value = data.is_active;

                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection