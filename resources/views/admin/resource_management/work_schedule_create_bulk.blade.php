@extends('layouts.app')

@section('content')
<div class="py-5 container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('work-schedule.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h6>Membuat Jadwal Kerja</h6>
                    </div>

                    <form id="batchForm" action="{{ route('work-schedule.batch-store') }}" method="POST">
                        @csrf

                        {{-- Bulan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Pilih Bulan</label>
                                <input type="month" id="month-picker" name="month" class="form-control" value="{{ now()->format('Y-m') }}">
                            </div>
                            <div class="col-md-6">
                                <label>Hari Mulai Pola Kerja</label>
                                <select id="start_day" name="start_day" class="form-select" required>
                                    <option value="1">Senin</option>
                                    <option value="2">Selasa</option>
                                    <option value="3">Rabu</option>
                                    <option value="4">Kamis</option>
                                    <option value="5">Jumat</option>
                                    <option value="6">Sabtu</option>
                                    <option value="0">Minggu</option>
                                </select>
                            </div>
                        </div>

                        {{-- Users --}}
                        <div class="mb-3">
                            <label>Pilih Karyawan</label>
                            <select name="users[]" class="form-control" multiple required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small>(Ctrl/Cmd untuk pilih lebih dari 1)</small>
                        </div>

                        <div class="row">
                        {{-- Shift Pattern --}}
                            <div class="col-12 col-md-3 mb-3">
                                <label>Pola Shift</label>
                                <div id="shift-patterns">
                                    <div class="input-group mb-2">
                                        <select name="shift_pattern[]" class="form-control">
                                            <option value="">Libur</option>
                                            @foreach($workingTimes as $wt)
                                                <option color="{{ $wt->color }}" value="{{ $wt->id }}">{{ $wt->name }} ({{ $wt->start_time }}-{{ $wt->end_time }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-danger remove-shift">Hapus</button>
                                    </div>
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
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
{{--  
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addBtn = document.getElementById('add-shift');
        const container = document.getElementById('shift-patterns');
        const monthPicker = document.getElementById('month-picker');
        const startDaySelect = document.getElementById('start_day');
        const calendarBody = document.getElementById('calendar-body');

        console.log(container, monthPicker, startDaySelect);

        // Data working times untuk mapping nama shift
        const workingTimes = @json($workingTimes);
        
        // Tambah shift
        addBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `
                <select name="shift_pattern[]" class="form-control">
                    <option value="">Libur</option>
                    @foreach($workingTimes as $wt)
                        <option value="{{ $wt->id }}">{{ $wt->name }} ({{ $wt->start_time }}-{{ $wt->end_time }})</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-danger remove-shift">Hapus</button>
            `;
            container.appendChild(div);
            div.querySelector('.remove-shift').addEventListener('click', () => {
                div.remove();
                renderCalendar();
            });
            renderCalendar();
        });

        // Remove button awal
        document.querySelectorAll('.remove-shift').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.parentElement.remove();
                renderCalendar();
            });
        });

        monthPicker.addEventListener('change', renderCalendar);
        startDaySelect.addEventListener('change', renderCalendar);
        container.addEventListener('change', renderCalendar);

        function renderCalendar() {
            const [year, month] = monthPicker.value.split('-').map(Number);
            const firstDate = new Date(year, month-1, 1);
            const lastDate = new Date(year, month, 0);
            const totalDays = lastDate.getDate();

            // Ambil pola shift dari form
            const shiftPattern = Array.from(container.querySelectorAll('select')).map(s => s.value ? parseInt(s.value) : null);
            const startDay = parseInt(startDaySelect.value); // 0=Sun ... 6=Sat

            if (shiftPattern.length === 0) {
                calendarBody.innerHTML = '<tr><td colspan="7" class="text-center">Tambahkan pola shift terlebih dahulu</td></tr>';
                return;
            }

            calendarBody.innerHTML = '';
            let week = [];

            // Tentukan hari pertama minggu di kalender (0=Senin, 1=Selasa, ..., 6=Minggu)
            const weekStart = 0; // Senin

            // Hitung offset untuk padding di minggu pertama
            const firstDayWeekday = firstDate.getDay(); // 0=Sun ... 6=Sat
            const offset = (firstDayWeekday - weekStart + 7) % 7;
            for(let i = 0; i < offset; i++) {
                week.push('<td class="text-muted"></td>');
            }

            // Cari tanggal pertama yang sesuai start_day pola shift
            let firstPatternDate = new Date(year, month-1, 1);
            while (firstPatternDate.getDay() !== startDay) {
                firstPatternDate.setDate(firstPatternDate.getDate() + 1);
            }

            for(let day = 1; day <= totalDays; day++) {
                const date = new Date(year, month-1, day);

                // Hitung selisih hari dari tanggal pattern pertama
                const daysSincePatternStart = Math.floor((date - firstPatternDate) / (1000 * 60 * 60 * 24));

                // Tentukan indeks shift (modulus aman untuk negatif)
                let shiftIndex = ((daysSincePatternStart % shiftPattern.length) + shiftPattern.length) % shiftPattern.length;
                let shiftId = shiftPattern[shiftIndex];

                let style = '';
                let text;
                if (shiftId === null) {
                    style = 'background-color: #e9ecef;'; // libur
                    text = 'Libur';
                } else {
                    // Cari <option> yang sesuai shiftId untuk ambil warna
                    const option = Array.from(container.querySelectorAll('select')[0].options).find(o => parseInt(o.value) === shiftId);
                    const color = option?.getAttribute('color') || '#0d6efd';
                    style = `background-color: ${color}; color: white;`;
                    text = workingTimes.find(wt => wt.id === shiftId)?.name || 'Libur';
                }

                week.push(`<td style="${style}">${day}<br><small>${text}</small></td>`);

                // Jika hari terakhir minggu, push baris ke kalender
                if ((week.length % 7 === 0)) {
                    calendarBody.innerHTML += `<tr>${week.join('')}</tr>`;
                    week = [];
                }
            }

            // Tambahkan baris terakhir jika tidak penuh
            if(week.length) {
                while(week.length < 7) {
                    week.push('<td class="text-muted"></td>');
                }
                calendarBody.innerHTML += `<tr>${week.join('')}</tr>`;
            }
        }

        // Render kalender saat halaman dimuat
        renderCalendar();
    });
</script> --}}
@include('admin.resource_management.partials.shift-calendar-js', ['shiftPattern' => $shiftPattern, 'workingTimes' => $workingTimes, 'month' => $month, 'startDay' => $startDay])

@endsection
