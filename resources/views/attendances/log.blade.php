@extends('layouts.app')

@section('content') 
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('attendances.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        {{-- <a class="btn btn-light" href="{{ route('work-schedule.batch-create') }}"><i class="bi bi-plus"></i></a> --}}
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Riwayat Absensi</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-end">
                            <div class="mb-2 col-12 col-md-4 col-lg-3">
                                <div class="input-group">
                                    <input type="month" id="month" name="month" value="{{ $month }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>    
                            </div>    
                            {{-- <div class="mb-2 col-2 col-md-8 col-lg-9 justify-content-end d-flex">
                                <a href="{{ route('work-schedule.export', ['month'=>$month]) }}" class="btn btn-success"><i class="bi bi-file-earmark-spreadsheet"></i></a>
                            </div> --}}
                        </div>    
                    </form>
                    <div class="table-responsive">
                          <table class="table table-bordered table-striped text-center">
                            <thead>
                                <tr> 
                                    <th>Hari/Tanggal</th> 
                                    <th>Jadwal Kerja</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Status</th> 
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no=1 ; @endphp
                                @forelse ($data as $row)
                                <tr> 
                                    <td>{{ \Carbon\Carbon::parse($row->work_date)->locale('id')->isoFormat('dddd, DD-MM') }}</td>

                                    <td>{{ $row?->workingTime?->start_time }} - {{ $row?->workingTime?->end_time  }}</td>  
                                    <td>{{ $row?->attendance?->clock_in_time }}</td> 
                                    <td>{{ $row?->attendance?->clock_out_time }}</td> 
                                    <td>{{ ucwords($row?->attendance?->status) }}</td>  
                                    <td> 
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3"> 
                        {{-- {{ $work_schedules->links('pagination::bootstrap-5') }} --}}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection