<div class="modal fade" id="modalDeductions" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="deductionForm" data-submit-url="{{ route('level.deductions.store', $level->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Deduction Level: {{ $level->name }} (Grade {{ $level->grade }})</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="deductionTable">
              <thead>
                <tr>
                  <th>Nama Deduction</th>
                  <th>Type</th>
                  <th>Value</th>
                  <th>Aktif</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($deductions as $ded)
                <tr>
                  <td><input type="text" name="name[]" class="form-control" value="{{ $ded->name }}" required></td>
                  <td>
                    <select name="type_value[]" class="form-select" required>
                      <option value="fixed" {{ $ded->type_value=='fixed' ? 'selected':'' }}>Fixed</option>
                      <option value="percent" {{ $ded->type_value=='percent' ? 'selected':'' }}>Percent</option>
                    </select>
                  </td>
                  <td><input type="text" name="value[]" class="form-control" value="{{ $ded->value }}" placeholder="Contoh percent = 2.5 atau fixed 200000" required></td>
                  <td class="text-center">
                    <input type="checkbox" name="is_active[]" value="1" class="form-check-input" {{ $ded->is_active ? 'checked' : '' }}>
                  </td>
                  <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                </tr>
                @empty
                <tr>
                  <td><input type="text" name="name[]" class="form-control" required></td>
                  <td>
                    <select name="type_value[]" class="form-select" required>
                      <option value="fixed">Fixed</option>
                      <option value="percent">Percent</option>
                    </select>
                  </td>
                  <td><input type="text" name="value[]" class="form-control" placeholder="Contoh percent = 2.5 atau fixed 200000" required></td>
                  <td class="text-center"><input type="checkbox" name="is_active[]" value="1" class="form-check-input" checked></td>
                  <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <button type="button" class="btn btn-sm btn-primary" id="addDeductionRow"><i class="bi bi-plus-lg"></i> Tambah Komponen</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Semua</button>
        </div>
      </form>
    </div>
  </div>
</div>