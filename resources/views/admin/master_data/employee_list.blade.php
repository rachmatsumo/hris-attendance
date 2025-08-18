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
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('user.store') }}" title="Tambah Data Karyawan" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Karyawan</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">ID</th>
                                    <th class="v-middle">Nama</th>
                                    <th class="v-middle">Jabatan</th>
                                    <th class="v-middle">Divisi</th>
                                    <th class="v-middle">Email</th>
                                    <th class="v-middle">HP</th>
                                    <th class="v-middle">Gender</th>
                                    <th class="v-middle">Status</th>
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $a)
                                <tr>
                                    <td class="v-middle">{{ $a->employee_id }}</td>
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td class="v-middle">{{ $a->position->name }}</td>
                                    <td class="v-middle">{{ $a->department->name }}</td>
                                    <td class="v-middle">{{ $a->email }}</td>
                                    <td class="v-middle">{{ $a->phone }}</td>
                                    <td class="v-middle">{{ $a->gender_locale }}</td>
                                    <td class="v-middle">{{ $a->status_name }}</td>
                                    <td class="v-middle">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInput"
                                                    method="put"
                                                    title="Edit Karyawan"
                                                    data-id="{{ $a->id }}"
                                                    data-url="{{ route('user.update', $a->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('user.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
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
                    <div class="pagination justify-content-center mt-3"> 
                        {{ $employees->links('pagination::bootstrap-5') }}
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
                <label for="date" class="form-label">ID Karyawan</label>
                <input type="text" class="form-control" id="employee_id" name="employee_id">
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Karyawan</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Divisi</label>
                <select class="form-select" id="department_id" name="department_id" required>
                <option value="" selected disabled>Pilih Divisi</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="position_id" class="form-label">Jabatan</label>
                <select class="form-select" id="position_id" name="position_id" required>
                    <option value="" selected disabled>Pilih Jabatan</option> 
                </select>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">No. Handphone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            
            <div class="mb-3">
                <label for="join_date" class="form-label">TMT (Tanggal bergabung)</label>
                <input type="date" class="form-control" id="join_date" name="join_date"> 
            </div>

            <div class="mb-3"> 
                <label class="form-label" for="gender">Gender</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">::Pilih Gender::</option>
                    <option value="male">Laki-Laki</option>
                    <option value="female">Perempuan</option>
                </select>
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
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_id');
    const positionSelect = document.getElementById('position_id');

    // Function load positions by department
    function loadPositions(departmentId, selectedPositionId = null) {
        if(!departmentId) return;
        fetch(`/user/position/${departmentId}`)
            .then(res => res.json())
            .then(data => {
                positionSelect.innerHTML = '<option value="" disabled selected>Pilih Jabatan</option>';
                data.forEach(pos => {
                    const opt = document.createElement('option');
                    opt.value = pos.id;
                    opt.textContent = pos.name;
                    if(selectedPositionId && selectedPositionId == pos.id) opt.selected = true;
                    positionSelect.appendChild(opt);
                });
            })
            .catch(err => console.error(err));
    }

    // Saat Create: pilih department baru
    departmentSelect.addEventListener('change', function() {
        const deptId = this.value;
        loadPositions(deptId);
    });

    // Saat Edit: load data dari server
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.editDataBtn');
        if(!btn) return;

        const id = btn.dataset.id;
        const url = `/user/${id}`;
        const form = document.getElementById('inputForm');
        const methodField = document.getElementById('methodField');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                form.action = url;
                methodField.innerHTML = '@method("PUT")';

                form.querySelector('#employee_id').value = data.employee_id ?? '';
                form.querySelector('#name').value = data.name ?? '';
                form.querySelector('#phone').value = data.phone ?? '';
                form.querySelector('#email').value = data.email ?? '';
                form.querySelector('#gender').value = data.gender ?? '';
                form.querySelector('#join_date').value = data.join_date ?? '';
                form.querySelector('#is_active').value = data.is_active ?? ''; 

                // Load positions dulu baru set department
                loadPositions(data.department_id, data.position_id);
                // Setelah posisi muncul, set department
                setTimeout(() => {
                    form.querySelector('#department_id').value = data.department_id ?? '';
                }, 100);
            })
            .catch(err => console.error(err));
    });
});
</script>

@endsection