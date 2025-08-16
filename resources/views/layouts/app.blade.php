<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="author" content="{{ config('app.author', 'Abdul Rachmat') }}"> 
    <meta name="description" content="{{ config('app.description', 'Human Resources Information System') }}">
    <meta name="keywords" content="HRIS, absensi, attendance, izin, employee, karyawan, management">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HRIS') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js']) 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
</head>
<body>
     <div id="loader-overlay">
        <div class="spinner mb-2"></div> 
        {{ config('app.description', 'Human Resources Information System') }}
        <span>by {{ config('app.author', 'Abdul Rachmat') }}</span>
    </div>
    
    <div id="app">
        <nav class="navbar navbar-top navbar-expand-md navbar-light bg-gradient-blue border-none">
            <div class="container-fluid px-4 flex-column align-items-start">
                <a class="navbar-brand text-white fs-6 mb-2" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="d-flex justify-content-between py-2 w-100">
                    <div class="d-flex flex-column">
                        <h4>{{ @Auth::user()->name }}</h4>
                        <span class="fs-7">{{ @Auth::user()->position->name }} <br> {{ Auth::user()->department->name }}</span>
                    </div>
                    <img src="{{ @Auth::user()->profile_photo 
                        ? asset('upload/avatar/' . @Auth::user()->profile_photo) 
                        : asset('upload/avatar/default.png') }}" 
                        class="avatar">
                </div> 
            </div>
        </nav>

        <nav class="bottom-bar">
            <div class="nav d-flex justify-content-around">
                <a href="{{ url('/') }}" 
                class="nav-link {{ request()->routeIs('dashboard') || request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-house-fill"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('attendances.index') }}" 
                class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('permits.index') }}" 
                class="nav-link {{ request()->routeIs('permits.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Izin</span>
                </a>
                
                @if(Auth::user()->role=='admin' || Auth::user()->role=='hr') 
                    <a href="{{ route('admin.index') }}" 
                    class="nav-link {{ request()->routeIs('admin.*') || 
                                        request()->routeIs('work-schedule.*') ||
                                        request()->routeIs('location.*') ||
                                        request()->routeIs('department.*') ||
                                        request()->routeIs('user.*') ||
                                        request()->routeIs('position.*') ||
                                        request()->routeIs('holiday.*') ||
                                        request()->routeIs('working-time.*') ||
                                        request()->routeIs('setting.*') ||
                                        request()->routeIs('payroll.*') ||
                                        request()->routeIs('recap-attendance.*')
                                        
                                        ? 'active' : '' }}">
                        <i class="bi bi-device-hdd"></i>
                        <span>Admin</span>
                    </a>
                @endif

                <a href="{{ route('account.index') }}" 
                class="nav-link {{ request()->routeIs('account.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Akun</span>
                </a>
            </div>
        </nav>

        <main>
            <div class="container-fluid">
                {{-- Flash Message Success --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                        {{ session('status') }}
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show flash-message" role="alert">
                        {{ session('success') }}
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show flash-message" role="alert">
                        {{ session('error') }}
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger flash-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const path = window.location.pathname; 
            const loader = document.getElementById("loader-overlay");

            if (path === "/" || path === "/dashboard" || path === "/home") {
                setTimeout(function () {
                    loader.style.display = "none";
                    if (loader) {
                        loader.style.display = "none";
                    }
                }, 1000);  
            }else{
                loader.style.display = "none";
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const flash = document.querySelector(".flash-message");
            if (flash) {
                setTimeout(() => {
                    flash.style.transition = "opacity 0.5s ease";
                    flash.style.opacity = "0";
                    setTimeout(() => flash.remove(), 500);
                }, 2000);
            }
        });
    </script> 

    <script>
        $(document).on('click', '.openModalInputBtn', function(e) {
            var form = document.getElementById('inputForm');
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            var method = $(this).attr('method');
            var title = $(this).attr('title');

            console.log(url, method, title);
            $('#modalInput .modal-title').text(title);

            if(method=='post'){
                form.reset();
                $('#modalInput form').attr('action', url);
                $('#methodField').html('');
            } 
        });
    </script>
</body>
</html>
