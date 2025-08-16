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
                        {{-- <a class="btn btn-light" href="#"><i class="bi bi-plus"></i></a> --}}
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Rekap Absensi</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-between align-items-center">
 
                            <div class="order-2 order-md-1 mb-2 col-12 col-md-5 col-lg-4">
                                <div class="input-group">
                                    <select name="type" class="form-select" id="filterType">
                                        <option value="daily" {{ request('type', 'daily') == 'daily' ? 'selected' : '' }} data-route="{{ route('recap-attendance.export.daily') }}">Harian</option>
                                        <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }} data-route="{{ route('recap-attendance.export.monthly') }}">Bulanan</option> 
                                    </select> 
                                    <input type="select_period" id="select_period" name="select_period" value="{{ $selectPeriod }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>
                            </div>

                            <div class="order-1 order-md-2 mb-2 col-12 col-md-1 justify-content-end d-flex">
                                <a href="{{ route('recap-attendance.export.daily', request()->all()) }}" class="btn btn-success" id="exportBtn">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                </a>
                            </div>
                        </div>    
                    </form>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">No</th>
                                    <th class="v-middle">Tanggal</th>
                                    <th class="v-middle">ID</th>
                                    <th class="v-middle">Nama Karyawan</th>
                                    <th class="v-middle">Jadwal Kerja</th> 
                                    <th class="v-middle">Masuk</th> 
                                    <th class="v-middle">Pulang</th> 
                                    <th class="v-middle">Status</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($recap_attendances as $a)
                                <tr>
                                    <td class="v-middle">{{ $no++ }}</td>
                                    <td class="v-middle">{{ date('Y-m-d', strtotime($a->date)) }}</td>
                                    <td class="v-middle">{{ $a->user->id }}</td>
                                    <td class="v-middle">{{ $a->user->name }}</td>
                                    <td class="v-middle">{{ $a?->workSchedule?->workingTime?->start_time }} - {{ $a?->workSchedule?->workingTime?->end_time }}</td>
                                    <td class="v-middle">{{ $a->clock_in_time }} @if($a->clock_in_time)<button class="btn btn-secondary btn-light btn-sm evidenceBtn" data-url="{{ $a->clock_in_photo }}"><i class="bi bi-eye"></i></button> @endif</td> 
                                    <td class="v-middle">{{ $a->clock_out_time }} @if($a->clock_out_time)<button class="btn btn-secondary btn-light btn-sm evidenceBtn" data-url="{{ $a->clock_out_photo }}"><i class="bi bi-eye"></i></button> @endif</td>  
                                    <td class="v-middle">{{ ucwords($a->status) }}</td> 
                                    <td>
                                        
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
                        {{ $recap_attendances->links('pagination::bootstrap-5') }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="" method="POST" id="inputForm">
        <!-- CSRF token jika Laravel -->
        @csrf
        <div id="methodField"></div>
        <div class="modal-header">
          <h5 class="modal-title" id="evidenceModalLabel">Foto Absensi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
             <img id="evidence" class="w-100 thumbnail" src="">
        </div> 
      </form>
    </div>
  </div>
</div> 

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterType = document.getElementById('filterType');
        const selectPeriod = document.getElementById('select_period'); // input tunggal
        const exportBtn = document.getElementById('exportBtn');

        function toggleInput() {
            if(filterType.value === 'monthly') {
                selectPeriod.type = 'month';
            } else {
                selectPeriod.type = 'date';
            }

            // Update export href berdasarkan selected option
            const route = filterType.options[filterType.selectedIndex].dataset.route;
            exportBtn.setAttribute('href', route + '?' + new URLSearchParams({ 
                [selectPeriod.name]: selectPeriod.value 
            }).toString());
        }

        // Inisialisasi saat page load
        toggleInput();

        // Event change dropdown
        filterType.addEventListener('change', toggleInput);
        selectPeriod.addEventListener('change', toggleInput);
    });


    $(document).on('click', '.evidenceBtn', function(){
        var dataUrl = $(this).data('url'); // gunakan data-url attribute
        var baseUrl = "{{ asset('') }}"; // Blade asset dijadikan string JS

        $('#evidenceModal').modal('show');

        // Misal #evidence adalah <img>
        $('#evidence').attr('src', baseUrl + dataUrl);
    });
</script>
@endsection