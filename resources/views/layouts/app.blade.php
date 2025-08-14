<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js']) 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

    <style>
        .flash-message {
            position: fixed!important;
            bottom: var(--navbar-height, 100px);
            left: 5%;
            width : 90%;
            /* transform: translateX(-50%); */
            z-index: 1055; 
            padding: 12px 20px;
            border-radius: 8px;
        }
        .avatar{
            width:50px;
            height:50px;
            border-radius:100%;
            object-fit:cover;
            object-position: top; /* fokus ke atas */
            background:white;
            padding : 4px;
        }
        .bg-gradient-blue{
            background: #1d6ee5;
            color: white !important;
        }
        .fs-67{
            font-size: .7rem !important;
        }
        .navbar-top{
            position: fixed!important;
            top:0;
            left :0;
            width:100%;
            z-index: 1050;
        }
        body {
            padding-top: var(--navbar-height, 120px);
            padding-bottom: var(--navbar-height, 60px);
        }
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            z-index: 1000;
        }
        .bottom-bar .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 0;
            color: #6c757d;
            font-size: 0.85rem;
        }
        .bottom-bar .nav-link i {
            font-size: 1rem;
            margin-bottom: 3px;
        }
        .bottom-bar .nav-link span {
            font-size: .7rem; 
        }
        .bottom-bar .nav-link.active {
            color: #1d6ee5;
        }
        .v-middle{
            vertical-align:middle;
        }
          .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .menu-list li {
            border-bottom: 1px solid #ddd;
        }
        .menu-list a {
            display: flex;
            justify-content : space-between;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            background: #fff;
            transition: background 0.2s;
        }
        .menu-list a span {
            margin-right: 12px;
            font-size: 18px;
        }
        .menu-list a:hover {
            background: #f5f5f5;
        }
        .menu-list a.logout {
            color: red;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-top navbar-expand-md navbar-light bg-gradient-blue border-none">
            <div class="container flex-column align-items-start">
                <a class="navbar-brand text-white fs-6 mb-2" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="d-flex justify-content-between py-2 w-100">
                    <div class="d-flex flex-column">
                        <h4>{{ @Auth::user()->name }}</h4>
                        <span class="fs-7">{{ @Auth::user()->department->name }}</span>
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
                class="nav-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('permits.index') }}" 
                class="nav-link {{ request()->routeIs('permits.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Izin</span>
                </a>
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

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
