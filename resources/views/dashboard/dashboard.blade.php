@extends('layouts.app')

@section('content')
<div class="py-5 container">

    <div class="row">
 
        <!-- Stats Cards --> 
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="stat-number">{{ $statistics->total_schedule }}</h3>
                        <p class="stat-label">Total Jadwal Kerja</p> 
                        @php
                            $growth = $statistics->growth_schedule;
                            $isUp = $growth >= 0;
                        @endphp

                        <span class="{{ $isUp ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isUp ? 'bi-arrow-up' : 'bi-arrow-down' }} me-1"></i>
                            {{ $growth }}%
                        </span>
                    </div>
                    <div class="stat-icon" style="background: #6366f1;">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="stat-number">{{ $statistics->total_present }}</h3>
                        <p class="stat-label">Total Kehadiran</p> 
                        @php
                            $growth = $statistics->growth_present;
                            $isUp = $growth >= 0;
                        @endphp

                        <span class="{{ $isUp ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isUp ? 'bi-arrow-up' : 'bi-arrow-down' }} me-1"></i>
                            {{ $growth }}%
                        </span>
                    </div>
                    <div class="stat-icon" style="background: #10b981;">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="stat-number">{{ $statistics->total_late }}</h3>
                        <p class="stat-label">Total Terlambat</p> 
                        @php
                            $growth = $statistics->growth_late;
                            $isUp = $growth >= 0;
                        @endphp

                        <span class="{{ $isUp ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isUp ? 'bi-arrow-up' : 'bi-arrow-down' }} me-1"></i>
                            {{ $growth }}%
                        </span>
                    </div>
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="stat-number">{{ $statistics->total_absent }}</h3>
                        <p class="stat-label">Total Tidak Hadir</p>
                        @php
                            $growth = $statistics->growth_absent;
                            $isUp = $growth >= 0;
                        @endphp

                        <span class="{{ $isUp ? 'text-success' : 'text-danger' }}">
                            <i class="bi {{ $isUp ? 'bi-arrow-up' : 'bi-arrow-down' }} me-1"></i>
                            {{ $growth }}%
                        </span>
                    </div>
                    <div class="stat-icon" style="background: #ef4444;">
                        <i class="bi bi-calendar2-x"></i>
                    </div>
                </div>
            </div>
        </div> 
    
        <div class="col-12 col-lg-6 col-md-6 mb-4 d-flex">
            <div class="table-container w-100">
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terakhir</h5> 
                    </div>
                </div>
                
                <div class="table-responsive p-3">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Karyawan</th> 
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $d)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $d->user->profile_photo 
                                                ? asset('upload/avatar/' . $d->user->profile_photo) 
                                                : asset('upload/avatar/default.png') }}" 
                                                alt="Avatar" class="employee-avatar me-3">
                                        <div>
                                            <div class="fw-semibold">{{ $d->user->name }}</div>
                                            <small class="text-muted">{{ $d->user?->position?->name }}</small>
                                        </div>
                                    </div>
                                </td> 
                                <td>{{ $d->clock_in }}</td>
                                <td>{{ $d->clock_out }}</td>
                                <td>
                                    {!! $d->status_badge !!}
                                </td> 
                            </tr>  
                            @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    Belum ada data!
                                </td> 
                            </tr> 
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div> 

        @include('dashboard.partials.calendar', [ 'schedules'=>$schedules, 'dates'=>$dates ])

    </div>

</div> 
@endsection

