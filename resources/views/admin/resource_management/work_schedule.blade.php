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
                        <a class="btn btn-light" href="#"><i class="bi bi-plus"></i></a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Jadwal Kerja</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">No</th>
                                    <th class="v-middle">Nama Karyawan</th>
                                    <th class="v-middle">Hari</th>
                                    <th class="v-middle">Masuk</th> 
                                    <th class="v-middle">Pulang</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($work_schedules as $a)
                                <tr>
                                    <td class="v-middle">{{ $no++ }}</td>
                                    <td class="v-middle">{{ $a->user->name }}</td>
                                    <td class="v-middle">{{ $a->day_of_week_name }}</td>
                                    <td class="v-middle">{{ $a->start_time_formatted }}</td> 
                                    <td class="v-middle">{{ $a->end_time_formatted }}</td> 
                                    <td></td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3"> 
                        {{ $work_schedules->links('pagination::bootstrap-5') }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection