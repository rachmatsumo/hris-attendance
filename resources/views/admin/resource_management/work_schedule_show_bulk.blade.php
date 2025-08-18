@extends('layouts.app')

@section('content')
<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('work-schedule.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <a class="btn btn-light" href="{{ route('work-schedule.batch-edit', $bulk_id) }}"><i class="bi bi-pencil"></i></a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        @php
                            $firstDate = \Carbon\Carbon::parse($schedules->first()->work_date);
                        @endphp
                        <span>Bulan {{ $firstDate->format('F Y') }}</span>
                        <h6>{{ $bulk_id }}</h6>
                    </div> 

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-center" style="font-size:12px;">
                            <thead>
                                <tr>
                                    <th>Senin</th>
                                    <th>Selasa</th>
                                    <th>Rabu</th>
                                    <th>Kamis</th>
                                    <th>Jumat</th>
                                    <th>Sabtu</th>
                                    <th>Minggu</th>
                                </tr>
                            </thead>
                            <tbody id="calendar-body">
                                @php
                                    $firstDate = \Carbon\Carbon::parse($dates->first());
                                    $lastDate = \Carbon\Carbon::parse($dates->last());
                                    $currentDate = $firstDate->copy();
                                    $week = [];
                                @endphp

                                @while($currentDate <= $lastDate)
                                    @php
                                        $dayOfWeek = $currentDate->dayOfWeek; // 0=Sun ... 6=Sat
                                        $dayIndex = ($dayOfWeek + 6) % 7; // Konversi: Senin=0 ... Minggu=6
                                    @endphp

                                    {{-- Awal minggu pertama, tambahkan padding kosong --}}
                                    @if($currentDate->eq($firstDate) && $dayIndex > 0)
                                        @for($i=0; $i < $dayIndex; $i++)
                                            @php $week[] = '<td class="bg-light"></td>'; @endphp
                                        @endfor
                                    @endif

                                    {{-- Cell tanggal --}}
                                    @php
                                        $shift = $schedules->where('work_date', $currentDate->format('Y-m-d'))->first();
                                        $color = $shift?->workingTime?->color ?? '#e9ecef';
                                        $textColor = in_array($color, ['#ffc107','#e9ecef']) ? 'black' : 'white';
                                        $shiftText = $shift
                                            ? ($shift->workingTime?->code.' <br> '.$shift->workingTime?->name)
                                            : 'Libur';
                                        $week[] = '<td style="background-color:'.$color.'; color:'.$textColor.'; min-width:50px;">'.
                                                    $currentDate->format('d').'<br><small>'.$shiftText.'</small></td>';
                                    @endphp

                                    {{-- Jika hari Minggu, push baris --}}
                                    @if($dayIndex == 6)
                                        <tr>{!! implode('', $week) !!}</tr>
                                        @php $week = []; @endphp
                                    @endif

                                    @php $currentDate->addDay(); @endphp
                                @endwhile

                                {{-- Baris terakhir jika minggu tidak penuh --}}
                                @if(!empty($week))
                                    @php
                                        while(count($week) < 7) $week[] = '<td class="bg-light"></td>';
                                        echo '<tr>'.implode('', $week).'</tr>';
                                    @endphp
                                @endif

                            </tbody>
                        </table>
                    </div>

                    {{-- Info Karyawan --}}
                    <h6>Informasi Karyawan:</h6>
                    <table class="table table-bordered table-sm" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Total Hari Kerja</th>
                                <th>Total Hari Libur</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $userSchedules = $schedules->where('user_id', $user->id);
                                    $totalKerja = $userSchedules->whereNotNull('working_time_id')->count();
                                    $totalLibur = $userSchedules->whereNull('working_time_id')->count();
                                @endphp
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $totalKerja }}</td>
                                    <td>{{ $totalLibur }}</td>
                                    <td>
                                        {{-- Hapus per user dan edit per user (untuk split) --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
