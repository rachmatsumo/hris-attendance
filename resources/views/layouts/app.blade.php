<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2196F3">
    <meta name="description" content="Deskripsi aplikasi Laravel PWA">
    
    <!-- Manifest Link -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Apple Touch Icons (untuk iOS) -->
    <link rel="apple-touch-icon" href="{{ asset('img/icons/logo.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/icons/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icons/logo.png') }}">
    
    <!-- Apple Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    
    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileImage" content="{{ asset('img/icons/icon-152x152.png') }}">
    <meta name="msapplication-TileColor" content="#1d6ee5">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('company_name') }} - Human Resource Information System</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

   
</head>
<body>
    <!-- Konten aplikasi Laravel -->
    <div id="app">
        <nav class="navbar navbar-top navbar-expand-md navbar-light bg-gradient-blue border-none">
            <div class="container-fluid px-4 flex-column align-items-start">
                <a class="navbar-brand text-white fs-6 mb-2 d-flex justify-content-between w-100" href="{{ url('/') }}">
                   {{ setting('company_name') }} - {{ config('app.name', 'Laravel') }}
                   <!-- Install PWA Button in Navbar -->
                   {{-- <button id="install-btn-nav" style="display: none;" class="btn btn-sm btn-light rounded-pill">
                       <i class="bi bi-download"></i> Install
                   </button> --}}
                </a>

                <div class="d-flex justify-content-between py-2 w-100">
                    <div class="d-flex flex-column">
                        <h4>{{ @Auth::user()->name }}</h4>
                        <span class="fs-7">{{ @Auth::user()->position->name ?? '-' }} <br> {{ @Auth::user()->department->name ?? '-' }}</span>
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
                <a href="{{ route('attendance-permit.index') }}" 
                class="nav-link {{ request()->routeIs('attendance-permit.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Cuti & Izin</span>
                </a>
                
                @if(Auth::user()?->role=='admin' || Auth::user()?->role=='hr') 
                    <a href="{{ route('admin.index') }}" 
                    class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
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
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer bg-light border-top mt-5 py-4">
            <div class="container-fluid px-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <h6 class="mb-2">{{ setting('company_name') }} - HRIS</h6>
                        <small class="text-muted">
                            Human Resources Information System<br>
                            by {{ config('app.author', 'Abdul Rachmat') }}
                        </small>
                    </div>
                    <div class="col-12 col-md-6 text-center text-md-end">
                        <!-- Install PWA Button in Footer -->
                        {{-- <button id="install-btn-footer" style="display: none;" class="btn btn-primary mb-2 w-100 w-md-auto">
                            <i class="bi bi-download"></i> Install Aplikasi
                        </button> --}}
                        <div class="small text-muted">
                            <div>&copy; {{ date('Y') }} All Rights Reserved</div>
                            <div>Version 1.0.0</div>
                        </div>
                    </div>
                </div>
                
                <!-- PWA Install Prompt Card (lebih prominent) -->
                <div id="pwa-install-card" class="card border-primary mt-3" style="display: none;">
                    <div class="card-body text-center py-3">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-phone text-primary me-2 fs-4"></i>
                            <h6 class="mb-0">Install Aplikasi HRIS</h6>
                        </div>
                        <p class="small text-muted mb-3">
                            Install aplikasi ini di perangkat Anda untuk akses lebih cepat dan pengalaman yang lebih baik
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button id="install-btn-card" class="btn btn-primary btn-sm">
                                <i class="bi bi-download"></i> Install Sekarang
                            </button>
                            <button id="dismiss-install-card" class="btn btn-outline-secondary btn-sm">
                                Nanti Saja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
 
    
    <script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js"></script> 
    
    <!-- Service Worker Registration -->
    <script>
        firebase.initializeApp({
            apiKey: "AIzaSyBvzVzw7BVJvqJKQsOSjKEU4n0S7ZQOPIw",
            authDomain: "busogi-ee864.firebaseapp.com",
            projectId: "busogi-ee864",
            storageBucket: "busogi-ee864.firebasestorage.app",
            messagingSenderId: "558198634073",
            appId: "1:558198634073:web:41835adbd3bc59bc522348"
        }); 

        const messaging = firebase.messaging(); 
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                // console.log('Notification permission granted.'); 
                messaging.getToken({ vapidKey: 'BHVE9HSjZe000Axq3nLWvosBis_ztbSe-SQoFajMAczdbqm92Q1uKJXfHf-ctoriZhGD1ZHVRJvrTIy5_PXfcLE'}).then((currentToken) => {
                    if (currentToken) {
                        // console.log('Token retrieved:', currentToken);
                        saveToken(currentToken);  
                    } else {
                        // console.log('No registration token available.');
                    }
                }).catch((err) => {
                    // console.log('An error occurred while retrieving token.', err);
                });
            } else {
                // console.log('Unable to get permission to notify.');
            }
        }); 

        messaging.onMessage((payload) => {
            console.log('[Firebase] Foreground message ', payload);

            const title = payload.notification.title;
            const body  = payload.notification.body;
            const image = payload.notification.image; // opsional

            if (image) {
                // kalau ada gambar
                Swal.fire({
                    title: title,
                    text: body,
                    imageUrl: image,
                    imageWidth: 100,
                    imageHeight: 100,
                    timer: 5000,
                    showConfirmButton: false
                });
            } else {
                // kalau tidak ada gambar
                Swal.fire({
                    title: title,
                    text: body,
                    icon: 'info',
                    timer: 5000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }
        });

         
        function saveToken(token){ 
            console.log(token);
            $.ajax({
                url: "{{ route('account.save-fcm-token') }}",
                method: "POST",
                dataType: "json",
                data: {
                    fcm_token : token,
                    _token : "{{ csrf_token() }}"
                },
                    success: function(data) {  
                        console.log(data); 
                },  
            })
        } 
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset("firebase-messaging-sw.js") }}')
                    .then((registration) => {
                        // console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        // console.log('SW registration failed: ', registrationError);
                    });
            });
        }

        // Install Prompt Handler
        let deferredPrompt;
        // const installBtnNav = document.getElementById('install-btn-nav');
        // const installBtnFooter = document.getElementById('install-btn-footer');
        const installBtnCard = document.getElementById('install-btn-card');
        const pwaInstallCard = document.getElementById('pwa-install-card');
        const dismissInstallCard = document.getElementById('dismiss-install-card');

        // Check if already installed
        function checkIfInstalled() {
            // Check if running in standalone mode (installed PWA)
            if (window.matchMedia('(display-mode: standalone)').matches || 
                window.navigator.standalone === true) {
                return true;
            }
            // Check if installed via browser API
            if ('getInstalledRelatedApps' in navigator) {
                navigator.getInstalledRelatedApps().then(apps => {
                    if (apps.length > 0) {
                        return true;
                    }
                });
            }
            return false;
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Don't show if already installed
            if (checkIfInstalled()) {
                return;
            }

            // Show install buttons
            // installBtnNav.style.display = 'inline-block';
            // installBtnFooter.style.display = 'inline-block';
            
            // Show install card with delay on homepage
            if (window.location.pathname === '/' || window.location.pathname === '/dashboard') {
                setTimeout(() => {
                    if (!localStorage.getItem('pwa-install-dismissed')) {
                        pwaInstallCard.style.display = 'block';
                    }
                }, 3000); // Show after 3 seconds
            }
        });

        // Handle install button clicks
        async function handleInstall() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response: ${outcome}`);
                
                if (outcome === 'accepted') {
                    // Hide all install prompts
                    hideInstallPrompts();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Aplikasi HRIS berhasil diinstall',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
                
                deferredPrompt = null;
            }
        }

        function hideInstallPrompts() {
            // installBtnNav.style.display = 'none';
            // installBtnFooter.style.display = 'none';
            pwaInstallCard.style.display = 'none';
        }

        // Add click listeners
        // if (installBtnNav) installBtnNav.addEventListener('click', handleInstall);
        // if (installBtnFooter) installBtnFooter.addEventListener('click', handleInstall);
        if (installBtnCard) installBtnCard.addEventListener('click', handleInstall);

        // Dismiss install card
        if (dismissInstallCard) {
            dismissInstallCard.addEventListener('click', () => {
                pwaInstallCard.style.display = 'none';
                localStorage.setItem('pwa-install-dismissed', 'true');
            });
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('App was installed');
            hideInstallPrompts();
            
            // Show success notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Aplikasi Terinstall!',
                    text: 'Aplikasi HRIS berhasil diinstall di perangkat Anda',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });

        // Analytics - Detect PWA launch
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('source') === 'pwa') {
            console.log('App launched from PWA');
            // Laravel analytics tracking
            @if(config('app.env') === 'production')
            // Kirim ke Google Analytics atau analytics lainnya
            @endif
        }

        // Hide install prompts if already installed
        if (checkIfInstalled()) {
            hideInstallPrompts();
        }
    </script>
</body>
</html>