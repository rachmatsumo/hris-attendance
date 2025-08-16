@extends('layouts.app')

@section('content') 

    <div class="py-5 bg-gray-100 min-h-screen">
        <ul class="menu-list mb-5">
            <span class="text-start text-muted">
                <h6 class="ms-3">Master Data</h6>
            </span>
            <li>
                <a href="{{ route('location.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-map mb-0"></i></span> Area Kerja
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('department.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-building mb-0"></i></span> Daftar Divisi
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('position.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-person-badge mb-0"></i></span> Daftar Jabatan
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('user.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-people mb-0"></i></span> Daftar Karyawan
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('working-time.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-clock mb-0"></i></span> Jam Kerja
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('holiday.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-calendar-date mb-0"></i></span> Hari Libur
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('setting.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-gear mb-0"></i></span> Pengaturan
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
        </ul>

        <ul class="menu-list mb-4">
            <span class="text-start text-muted">
                  <h6 class="ms-3">Resource Management</h6>
            </span>
            <li>
                <a href="{{ route('work-schedule.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-calendar-week mb-0"></i></span> Jadwal Kerja
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('payroll.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-cash-stack mb-0"></i></span> Payroll Karyawan
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('recap-attendance.index') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-folder2-open mb-0"></i></span> Rekap Absensi
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
        </ul>
    </div>
@endsection