<div class="modal fade" id="modalIncomes" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="incomeForm" data-submit-url="{{ route('level.incomes.store', $level->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Income Level: {{ $level->name }} (Grade {{ $level->grade }})</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="incomeTable">
                    <thead>
                    <tr>
                        <th>Nama Income</th>
                        <th>Kategori</th>
                        <th>Value</th>
                        <th>Aktif</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($incomes as $inc)
                    <tr>
                        <td><input type="text" name="name[]" class="form-control" value="{{ $inc->name }}" required></td>
                        <td>
                            <select name="category[]" class="form-select" required>
                                <option value="base" @selected($inc->category == 'base')>Base</option>
                                <option value="daily" @selected($inc->category == 'daily')>Daily</option>
                            </select>
                        </td>
                        <td><input type="text" name="value[]" class="form-control" value="{{ $inc->value }}" placeholder="Contoh: 1000000" required></td>
                        <td class="text-center">
                        <input type="checkbox" name="is_active[]" value="1" class="form-check-input" {{ $inc->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                    </tr>
                    @empty
                    <tr>
                        <td><input type="text" name="name[]" class="form-control" required></td>
                        <td>
                            <select name="category[]" class="form-select" required>
                                <option value="base">Base</option>
                                <option value="daily">Daily</option>
                            </select>
                        </td>
                        <td><input type="text" name="value[]" class="form-control" placeholder="Contoh: 1000000" required></td>
                        <td class="text-center"><input type="checkbox" name="is_active[]" value="1" class="form-check-input" checked></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="addIncomeRow"><i class="bi bi-plus-lg"></i> Tambah Komponen</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Semua</button>
        </div>
      </form>
    </div>
  </div>
</div>