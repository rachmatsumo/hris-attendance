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
                        <a class="btn btn-light" href="{{ route('work-schedule.batch-create') }}"><i class="bi bi-plus"></i></a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Jadwal Kerja</h6>
                    </div>
                    <form method="GET" class="mb-3 mt-3">  
                        <div class="row d-flex justify-content-end">
                            <div class="mb-2 col-10 col-md-4 col-lg-3">
                                <div class="input-group">
                                    <input type="month" id="month" name="month" value="{{ $month }}" class="form-control">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>    
                            </div>    
                            <div class="mb-2 col-2 col-md-8 col-lg-9 justify-content-end d-flex">
                                <a href="{{ route('work-schedule.export', ['month'=>$month]) }}" class="btn btn-success"><i class="bi bi-file-earmark-spreadsheet"></i></a>
                            </div>
                        </div>    
                    </form>
                    <div class="table-responsive">
                          <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    {{-- <th>Batch</th> --}}
                                    <th>Karyawan</th>
                                    <th>Hari Kerja</th>
                                    <th>Hari Libur</th>
                                    <th>Hari Pertama</th>
                                    <th>Hari Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no=1 ; @endphp
                                @forelse ($bulkSchedules as $row)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    {{-- <td>{{ $row['bulk_id'] }}</td> --}}
                                    <td>{{ $row['karyawan'] }}{{ (str_word_count($row['karyawan']) > 5) ? '...' : '' }}</td>
                                    <td>{{ $row['total_hari_kerja'] }}</td>
                                    <td>{{ $row['total_hari_libur'] }}</td>
                                    <td>{{ $row['hari_pertama'] }}</td>
                                    <td>{{ $row['hari_terakhir'] }}</td>
                                    <td> 
                                        <div class="d-flex">
                                            <a href="{{ route('work-schedule.show', $row['bulk_id'] ?? 0) }}" class="btn btn-sm btn-info me-2"><i class="bi bi-eye"></i></a>
                                            {{-- Aksi hapus bulk id --}}
                                            <form action="{{ route('work-schedule.batch-destroy', $row['bulk_id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus bulk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash2"></i></button>
                                            </form>
                                        </div>
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
                    <div class="pagination flex-column justify-content-center mt-3"> 
                        {{ $bulkSchedules->links('pagination::bootstrap-5') }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection