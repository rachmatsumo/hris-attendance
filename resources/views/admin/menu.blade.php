@extends('layouts.app')

@section('content') 

    <div class="py-5 bg-gray-100 min-h-screen">
        <ul class="menu-list mb-5">
            <span class="text-start text-muted">
                <h6 class="ms-3">Master Data</h6>
            </span>
            <li>
                <a href="{{ route('admin.department') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-building mb-0"></i></span> Department
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.position') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-person-badge mb-0"></i></span> Position
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.karyawan') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-people mb-0"></i></span> Karyawan
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('admin.setting') }}">
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
                <a href="{{ route('admin.rekap-absensi') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-folder2-open mb-0"></i></span> Rekap Absensi
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.jadwal-kerja') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-calendar-week mb-0"></i></span> Jadwal Kerja
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
            <li>
                <a href="{{ route('admin.payroll') }}">
                    <div>
                        <span class="mb-0"><i class="bi bi-cash-stack mb-0"></i></span> Payroll
                    </div>
                    <i class="bi bi-chevron-compact-right"></i>
                </a>
            </li> 
        </ul>
    </div>
@endsection