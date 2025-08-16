@extends('layouts.app')

@section('content')
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('work-schedule.show', $bulk_id) }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h6>{{ $bulk_id }}</h6>
                    </div>

                    <form id="batchForm" action="{{ route('work-schedule.batch-update', $bulk_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Bulan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Pilih Bulan</label>
                                <input type="month" id="month-picker" name="month" class="form-control" value="{{ $month }}">
                            </div>
                            <div class="col-md-6">
                                <label>Hari Mulai Pola Kerja</label>
                                <select id="start_day" name="start_day" class="form-select" required>
                                    @for($i=0;$i<=6;$i++)
                                        <option value="{{ $i }}" {{ $startDay==$i?'selected':'' }}>
                                            {{ \Carbon\Carbon::create()->dayOfWeek($i)->locale('id')->dayName }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Users --}}
                        <div class="mb-3">
                            <label>Pilih User</label>
                            <select name="users[]" class="form-control" multiple required>
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small>(Ctrl/Cmd untuk pilih lebih dari 1)</small>
                        </div>

                        <div class="row">
                            {{-- Shift Pattern --}}
                            <div class="col-12 col-md-3 mb-3">
                                <label>Pola Shift</label>
                                <div id="shift-patterns">
                                    @foreach($shiftPattern as $sp)
                                        <div class="input-group mb-2">
                                            <select name="shift_pattern[]" class="form-control">
                                                <option value="">Libur</option>
                                                @foreach($workingTimes as $wt)
                                                    <option color="{{ $wt->color }}" value="{{ $wt->id }}"
                                                        {{ $sp==$wt->id?'selected':'' }}>
                                                        {{ $wt->name }} ({{ $wt->start_time }}-{{ $wt->end_time }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-danger remove-shift">Hapus</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="add-shift" class="btn btn-secondary btn-sm mt-1">+ Tambah Shift</button>
                            </div>

                            {{-- Kalender Preview --}}
                            <div class="col-12 col-md-9 mb-3">
                                <label>Pratinjau Kalender</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="font-size:10px;">
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
                                        <tbody id="calendar-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-end">
                            <button class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.resource_management.partials.shift-calendar-js', ['shiftPattern' => $shiftPattern, 'workingTimes' => $workingTimes, 'month' => $month, 'startDay' => $startDay])
@endsection
