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
                        <h6>Rekap Absensi</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">No</th>
                                    <th class="v-middle">Tanggal</th>
                                    <th class="v-middle">Nama Karyawan</th>
                                    <th class="v-middle">Masuk</th> 
                                    <th class="v-middle">Pulang</th> 
                                    <th class="v-middle">Status</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($recap_attendances as $a)
                                <tr>
                                    <td class="v-middle">{{ $no++ }}</td>
                                    <td class="v-middle">{{ date('Y-m-d', strtotime($a->date)) }}</td>
                                    <td class="v-middle">{{ $a->user->name }}</td>
                                    <td class="v-middle">{{ $a->clock_in_time }}</td> 
                                    <td class="v-middle">{{ $a->clock_out_time }}</td>  
                                    <td class="v-middle">{{ $a->status }}</td> 
                                    <td></td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3"> 
                        {{ $recap_attendances->links('pagination::bootstrap-5') }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection