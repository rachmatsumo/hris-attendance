@extends('layouts.app')

@section('content') 
<style>
.value-input.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.value-input {
    position: relative;
}

.value-input::placeholder {
    font-size: 0.875rem;
    color: #6c757d;
}

.value-input[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 0;
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 2px;
}
</style>

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
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('level.store') }}" title="Tambah Level" data-id=""><i class="bi bi-plus"></i></a>                        
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Daftar Level</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr> 
                                    <th class="v-middle">Grade</th>
                                    <th class="v-middle">Nama</th>
                                    <th class="v-middle">Option</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($levels as $a)
                                <tr> 
                                    <td class="v-middle">{{ $a->grade }}</td> 
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-light me-2 incomeBtn" data-id="{{ $a->id }}">Income</button>
                                            <button class="btn btn-sm btn-light me-2 deductionBtn" data-id="{{ $a->id }}">Deduction</button>
                                            <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInput"
                                                    method="put"
                                                    title="Edit Level"
                                                    data-id="{{ $a->id }}"
                                                    data-url="{{ route('level.update', $a->id) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('level.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
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
                        {{ $levels->links('pagination::bootstrap-5') }}
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
                <label for="name" class="form-label">Nama Level</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div> 
            <div class="mb-3">
                <label for="grade" class="form-label">Grade</label>
                <input type="number" class="form-control" id="grade" name="grade" required>
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
            const url = `/level/${id}`; // route show bisa dikustom
            const form = document.getElementById('inputForm');
            const methodField = document.getElementById('methodField');

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Set action form dan method
                    form.action = url;
                    methodField.innerHTML = '@method("PUT")';

                    // Isi field
                    form.querySelector('#name').value = data.name;
                    form.querySelector('#grade').value = data.grade; 
 
                })
                .catch(err => console.error(err));
        }
    }); 

    // Helper function to format and validate value input
    function formatValueInput(value) {
        if (!value || value.trim() === '') return null;
        
        // Remove whitespace
        value = value.trim();
        
        // Remove % symbol if present
        const hasPercent = value.includes('%');
        value = value.replace('%', '');
        
        // Replace comma with dot for decimal separator
        value = value.replace(',', '.');
        
        // Check if it's a valid number
        const numValue = parseFloat(value);
        if (isNaN(numValue)) return null;
        
        // Return formatted value (without % symbol for database storage)
        return numValue.toString();
    }

    // Function to format value based on type (fixed/percent)
    function formatValueByType(input, type) {
        const value = input.value.trim();
        if (value === '') return;
        
        const numValue = parseFloat(value.replace(',', '.').replace('%', ''));
        if (isNaN(numValue)) return;
        
        if (type === 'fixed') {
            // For fixed type, show as integer (no decimals)
            input.value = Math.round(numValue).toString();
        } else if (type === 'percent') {
            // For percent type, show with decimals if needed
            input.value = numValue.toString();
        }
    }
    
    // Function to format all existing deduction values based on their types
    function formatDeductionValues(modal) {
        const rows = modal.querySelectorAll('#deductionTable tbody tr');
        rows.forEach(row => {
            const typeSelect = row.querySelector('select[name="type_value[]"]');
            const valueInput = row.querySelector('.value-input');
            
            if (typeSelect && valueInput && valueInput.value.trim() !== '') {
                formatValueByType(valueInput, typeSelect.value);
            }
        });
    }

    // Function to format income values (always allow decimals for income)
    function formatIncomeValues(modal) {
        const valueInputs = modal.querySelectorAll('#incomeTable .value-input');
        valueInputs.forEach(input => {
            if (input.value.trim() !== '') {
                const numValue = parseFloat(input.value.replace(',', '.').replace('%', ''));
                if (!isNaN(numValue)) {
                    // For income, allow decimals
                    input.value = numValue.toString();
                }
            }
        });
    }
    function initializeValueInputValidation(container) {
        const valueInputs = container.querySelectorAll('.value-input');
        
        valueInputs.forEach(input => {
            // Add input event listener for real-time validation
            input.addEventListener('input', function() {
                const value = this.value;
                
                // Basic validation pattern: allow numbers, comma, dot, and % 
                const validPattern = /^[\d.,% ]*$/;
                
                if (!validPattern.test(value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            // Add blur event listener for format validation
            input.addEventListener('blur', function() {
                const value = this.value;
                if (value.trim() !== '') {
                    const formattedValue = formatValueInput(value);
                    if (formattedValue === null) {
                        this.classList.add('is-invalid');
                        this.title = 'Format tidak valid. Contoh: 100, 2.5, 2,5%';
                    } else {
                        this.classList.remove('is-invalid');
                        this.title = '';
                    }
                }
            });
            
            // Initial validation for existing values
            if (input.value.trim() !== '') {
                const formattedValue = formatValueInput(input.value);
                if (formattedValue === null) {
                    input.classList.add('is-invalid');
                    input.title = 'Format tidak valid. Contoh: 100, 2.5, 2,5%';
                }
            }
        });
    }

    // Global input event listener (as fallback)
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('value-input')) {
            const input = e.target;
            const value = input.value;
            
            // Basic validation pattern: allow numbers, comma, dot, and % 
            const validPattern = /^[\d.,% ]*$/;
            
            if (!validPattern.test(value)) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.incomeBtn')) {
            const btn = e.target.closest('.incomeBtn');
            const levelId = btn.dataset.id;
            
            fetch(`/level/${levelId}/incomes/modal`)
                .then(res => res.text())
                .then(html => {
                    // Remove existing modal if any
                    const existingModal = document.getElementById('modalIncomes');
                    if (existingModal) {
                        existingModal.remove();
                    }
                    
                    // Add new modal to body
                    document.body.insertAdjacentHTML('beforeend', html);
                    
                    // Initialize modal functionality
                    initializeIncomeModal();
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('modalIncomes'));
                    modal.show();
                })
                .catch(err => console.error(err));
        }
    });

    // Handle Deduction button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.deductionBtn')) {
            const btn = e.target.closest('.deductionBtn');
            const levelId = btn.dataset.id;
            
            fetch(`/level/${levelId}/deductions/modal`)
                .then(res => res.text())
                .then(html => {
                    // Remove existing modal if any
                    const existingModal = document.getElementById('modalDeductions');
                    if (existingModal) {
                        existingModal.remove();
                    }
                    
                    // Add new modal to body
                    document.body.insertAdjacentHTML('beforeend', html);
                    
                    // Initialize modal functionality
                    initializeDeductionModal();
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('modalDeductions'));
                    modal.show();
                })
                .catch(err => console.error(err));
        }
    });

    // Function to initialize income modal functionality
    function initializeIncomeModal() {
        const modal = document.getElementById('modalIncomes');
        if (!modal) return;

        // Initialize validation for existing inputs
        initializeValueInputValidation(modal);
        
        // Format existing income values
        formatIncomeValues(modal);

        // Add row functionality
        const addBtn = modal.querySelector('#addIncomeRow');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const tbody = modal.querySelector('#incomeTable tbody');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="name[]" class="form-control" required></td>
                    <td>
                        <select name="category[]" class="form-select" required>
                            <option value="base">Base</option>
                            <option value="daily">Daily</option>
                        </select>
                    </td>
                    <td><input type="text" name="value[]" class="form-control value-input" required placeholder="Contoh: 1000000"></td>
                    <td class="text-center"><input type="checkbox" name="is_active[]" value="1" class="form-check-input" checked></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                `;
                tbody.appendChild(row);
                
                // Initialize validation for the new row's value input
                const newValueInput = row.querySelector('.value-input');
                if (newValueInput) {
                    initializeValueInputValidation(row);
                }
            });
        }

        // Remove row functionality
        const table = modal.querySelector('#incomeTable');
        if (table) {
            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('removeRow')) {
                    const row = e.target.closest('tr');
                    if (table.querySelector('tbody').children.length > 1) {
                        row.remove();
                    } else {
                        alert('Minimal harus ada satu row');
                    }
                }
            });
        }

        // Form submit functionality
        const form = modal.querySelector('#incomeForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate and format value inputs before submit
                const valueInputs = form.querySelectorAll('.value-input');
                let isValid = true;
                
                valueInputs.forEach(input => {
                    const value = formatValueInput(input.value);
                    if (value === null) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        return;
                    }
                    input.classList.remove('is-invalid');
                    input.value = value;
                });
                
                if (!isValid) {
                    alert('Format nilai tidak valid. Gunakan format seperti: 100, 2.5, 2,5%, dll.');
                    return;
                }
                
                const formData = new FormData(this);
                const submitUrl = this.getAttribute('data-submit-url');
                
                fetch(submitUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Income berhasil disimpan');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                        modal.remove();
                        // Optionally reload the page or update the table
                        location.reload();
                    } else {
                        alert('Gagal menyimpan data');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan');
                });
            });
        }
    }

    // Function to initialize deduction modal functionality
    function initializeDeductionModal() {
        const modal = document.getElementById('modalDeductions');
        if (!modal) return;

        // Initialize validation for existing inputs
        initializeValueInputValidation(modal);
        
        // Format existing values based on type
        formatDeductionValues(modal);

        // Add row functionality
        const addBtn = modal.querySelector('#addDeductionRow');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const tbody = modal.querySelector('#deductionTable tbody');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="name[]" class="form-control" required></td>
                    <td>
                        <select name="type_value[]" class="form-select" required>
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percent</option>
                        </select>
                    </td>
                    <td><input type="text" name="value[]" class="form-control value-input" required placeholder="Contoh percent = 2.5 atau fixed 200000"></td>
                    <td class="text-center"><input type="checkbox" name="is_active[]" value="1" class="form-check-input" checked></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="bi bi-dash"></i></button></td>
                `;
                tbody.appendChild(row);
                
                // Initialize validation for the new row's value input
                const newValueInput = row.querySelector('.value-input');
                if (newValueInput) {
                    initializeValueInputValidation(row);
                }
                
                // Add change event for type select in new row
                const typeSelect = row.querySelector('select[name="type_value[]"]');
                const valueInput = row.querySelector('.value-input');
                typeSelect.addEventListener('change', function() {
                    formatValueByType(valueInput, this.value);
                });
            });
        }

        // Add change event listeners for existing type selects
        const typeSelects = modal.querySelectorAll('select[name="type_value[]"]');
        typeSelects.forEach(select => {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                const valueInput = row.querySelector('.value-input');
                formatValueByType(valueInput, this.value);
            });
        });

        // Remove row functionality
        const table = modal.querySelector('#deductionTable');
        if (table) {
            table.addEventListener('click', function(e) {
                if (e.target.classList.contains('removeRow')) {
                    const row = e.target.closest('tr');
                    if (table.querySelector('tbody').children.length > 1) {
                        row.remove();
                    } else {
                        alert('Minimal harus ada satu row');
                    }
                }
            });
        }

        // Form submit functionality
        const form = modal.querySelector('#deductionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate and format value inputs before submit
                const valueInputs = form.querySelectorAll('.value-input');
                let isValid = true;
                
                valueInputs.forEach(input => {
                    const value = formatValueInput(input.value);
                    if (value === null) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        return;
                    }
                    input.classList.remove('is-invalid');
                    input.value = value;
                });
                
                if (!isValid) {
                    alert('Format nilai tidak valid. Gunakan format seperti: 100, 2.5, 2,5%, dll.');
                    return;
                }
                
                const formData = new FormData(this);
                const submitUrl = this.getAttribute('data-submit-url');
                
                fetch(submitUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Deduction berhasil disimpan');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                        modal.remove();
                        // Optionally reload the page or update the table
                        location.reload();
                    } else {
                        alert('Gagal menyimpan data');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan');
                });
            });
        }
    }
</script>
@endsection