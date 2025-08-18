@extends('layouts.app')

@section('content') 

<div class="row">
    <div class="col-md-4 col-lg-3 col-12 bg-gradient-blue py-4"> 
        
        <form id="attendanceForm" action="{{ route('attendances.store') }}" method="POST" class="card p-4" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="photoData" name="photo" required>
            
            <div class="w-100 border-bottom mb-2">
                <div class="col-12 d-flex justify-content-between mb-3">
                    <div><b>Jadwal Kerja</b></div>
                    <div class="text-end">{{ \Carbon\Carbon::parse($activeShift?->work_date)->locale('id')->translatedFormat('D, d M Y') }}</div>
                </div>
                <div class="mb-3 text-center p-0">
                    <h1>
                        {{ optional($activeShift?->workingTime)->start_time ? \Carbon\Carbon::parse($activeShift?->workingTime->start_time)->format('H:i') : '-' }}
                        -
                        {{ optional($activeShift?->workingTime)->end_time ? \Carbon\Carbon::parse($activeShift?->workingTime->end_time)->format('H:i') : '-' }}
                    </h1>
                    <span>{{ $activeShift?->workingTime?->name }} - {{ $activeShift?->workingTime?->code }}</span>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between mt-2">
                <button type="button" 
                        class="btn btn-lg btn-primary me-2 rounded-1 w-50" 
                        id="btnClockIn"
                        data-start="{{ $clockInWindow['start'] ?? '' }}"
                        data-end="{{ $clockInWindow['end'] ?? '' }}"
                        onclick="openConfirmModal('clock_in_time')">
                    Masuk
                </button>
                <button type="button" 
                        class="btn btn-lg btn-light ms-2 rounded-1 w-50" 
                        id="btnClockOut"
                        data-start="{{ $clockOutWindow['start'] ?? '' }}"
                        data-end="{{ $clockOutWindow['end'] ?? '' }}"
                        onclick="openConfirmModal('clock_out_time')">
                    Pulang
                </button>
            </div>
        </form>

    </div>
    
    <div class="col-md-8 col-lg-9 col-12 py-4">
        <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
            <h6>Aktivitas</h6>
            <a class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 " href="{{ route('attendances.log') }}">Log absensi</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="v-middle">Tanggal</th>
                        <th class="v-middle">Jadwal Kerja</th>
                        <th class="v-middle">Clock In</th>
                        <th class="v-middle">Clock Out</th>
                        <th class="v-middle">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $a)
                    <tr>
                        <td class="v-middle">{{ $a->date->format('Y-m-d') }}</td>
                        <td class="v-middle">{{ optional($a?->workSchedule?->workingTime)?->start_time ? \Carbon\Carbon::parse($a->workSchedule->workingTime->start_time)->format('H:i') : '-' }} - {{ optional($a?->workSchedule?->workingTime)?->end_time ? \Carbon\Carbon::parse($a->workSchedule->workingTime->end_time)->format('H:i') : '-' }}</td>
                        <td class="v-middle">{{ $a->clock_in_time ? $a->clock_in_time->format('H:i:s') : '-' }}</td>
                        <td class="v-middle">{{ $a->clock_out_time ? $a->clock_out_time->format('H:i:s') : '-' }}</td>
                        <td class="v-middle">{{ ucfirst($a->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination justify-content-center mt-3"> 
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalText"></p>

                @if(setting('location_required') == '1')
                    <div id="map" style="height:300px;" class="mb-3"></div>
                    <input type="hidden" id="location_lat_long" name="location_lat_long">
                    <p class="small text-muted">Pastikan Anda berada di lokasi yang ditentukan.</p>
                @endif

                <!-- Camera Section -->
                <div class="mb-3">
                    <label class="form-label">Ambil Foto Real-time</label>
                    
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-success btn-sm" id="startCamera">
                            <i class="fas fa-camera"></i> Mulai Kamera
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="capturePhoto" disabled>
                            <i class="fas fa-camera-retro"></i> Ambil Foto
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="retakePhoto" style="display:none;">
                            <i class="fas fa-redo"></i> Ambil Ulang
                        </button>
                    </div>

                    <div class="camera-container mb-3" style="position: relative; max-width: 100%;">
                        <video id="video" width="100%" height="300" style="border: 2px solid #dee2e6; border-radius: 8px; display: none; max-width: 100%;" autoplay playsinline muted></video>
                        <canvas id="canvas" width="400" height="300" style="border: 2px solid #dee2e6; border-radius: 8px; display: none; max-width: 100%;"></canvas>
                        <div id="cameraLoading" style="display: none; text-align: center; padding: 50px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Mengakses kamera...</p>
                        </div>
                    </div>
                    
                    <div id="photoStatus" class="alert alert-warning" style="display: block;">
                        <i class="fas fa-exclamation-triangle"></i> Foto belum diambil. Silakan ambil foto terlebih dahulu.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    let selectedType = '';
    let map = null;
    let stream = null;
    let video = null;
    let canvas = null;
    let photoTaken = false;
    let isProcessing = false; // Tambah flag untuk prevent double processing

    function openConfirmModal(type) {
        selectedType = type;
        document.getElementById('modalText').textContent = 
            (type === 'clock_in_time') ? 'Apakah Anda yakin ingin melakukan clock in?' : 'Apakah Anda yakin ingin melakukan clock out?';

        resetCamera();

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();

        @if(setting('location_required') == '1')
        document.getElementById('confirmModal').addEventListener('shown.bs.modal', function () {
            initMap();
        }, { once: true });
        @endif
    }

    // Camera Functions
    function resetCamera() {
        photoTaken = false;
        isProcessing = false;
        document.getElementById('photoData').value = '';
        document.getElementById('photoStatus').style.display = 'block';
        document.getElementById('startCamera').disabled = false;
        document.getElementById('capturePhoto').disabled = true;
        document.getElementById('retakePhoto').style.display = 'none';
        document.getElementById('confirmBtn').disabled = false;
        document.getElementById('video').style.display = 'none';
        document.getElementById('canvas').style.display = 'none';
        document.getElementById('cameraLoading').style.display = 'none';
        
        // Stop existing stream
        if (stream) { 
            stream.getTracks().forEach(track => track.stop()); 
            stream = null; 
        }
    }

    document.getElementById('startCamera').addEventListener('click', async () => {
        if (isProcessing) return;
        isProcessing = true;
        
        document.getElementById('cameraLoading').style.display = 'block';
        document.getElementById('startCamera').disabled = true;
        
        try {
            // Enhanced camera constraints for mobile compatibility
            const constraints = {
                video: {
                    facingMode: { ideal: 'user' }, // Prefer front camera but fallback to any
                    width: { ideal: 640, max: 1280 },
                    height: { ideal: 480, max: 720 },
                    frameRate: { ideal: 30, max: 30 }
                }
            };
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video = document.getElementById('video');
            video.srcObject = stream;
            
            // Wait for video metadata to load
            video.onloadedmetadata = () => {
                console.log('Video metadata loaded');
                video.play().then(() => {
                    console.log('Video playing');
                    document.getElementById('cameraLoading').style.display = 'none';
                    video.style.display = 'block';
                    document.getElementById('capturePhoto').disabled = false;
                    isProcessing = false;
                }).catch(err => {
                    console.error('Error playing video:', err);
                    handleCameraError('Tidak dapat memulai video kamera');
                });
            };
            
            video.onerror = () => {
                console.error('Video error occurred');
                handleCameraError('Error pada video kamera');
            };
            
        } catch (err) {
            console.error('Camera access error:', err);
            handleCameraError('Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera dan menggunakan HTTPS.');
        }
    });

    function handleCameraError(message) {
        document.getElementById('cameraLoading').style.display = 'none';
        document.getElementById('startCamera').disabled = false;
        isProcessing = false;
        alert(message);
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    document.getElementById('capturePhoto').addEventListener('click', () => {
        if (isProcessing || !video || !stream) return;
        isProcessing = true;
        
        try {
            canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            
            // Get actual video dimensions
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;
            
            console.log('Video dimensions:', videoWidth, 'x', videoHeight);
            
            if (videoWidth === 0 || videoHeight === 0) {
                throw new Error('Video dimensions not available');
            }
            
            // Set canvas dimensions to match video
            canvas.width = videoWidth;
            canvas.height = videoHeight;
            
            // Draw the video frame to canvas
            context.drawImage(video, 0, 0, videoWidth, videoHeight);
            
            // Convert to base64 with compression for mobile
            const photoDataUrl = canvas.toDataURL('image/jpeg', 0.7); // Lower quality for mobile
            
            // Validate image data
            if (!photoDataUrl || photoDataUrl === 'data:,') {
                throw new Error('Failed to capture image data');
            }
            
            document.getElementById('photoData').value = photoDataUrl;

            // Update UI
            video.style.display = 'none';
            canvas.style.display = 'block';
            document.getElementById('capturePhoto').disabled = true;
            document.getElementById('retakePhoto').style.display = 'inline-block';
            document.getElementById('photoStatus').style.display = 'none';
            photoTaken = true;

            console.log('Photo captured successfully, data length:', photoDataUrl.length);

            // Stop stream after capture
            if (stream) { 
                stream.getTracks().forEach(track => track.stop()); 
                stream = null; 
            }
            
        } catch (err) {
            console.error('Error capturing photo:', err);
            alert('Gagal mengambil foto: ' + err.message);
            photoTaken = false;
        } finally {
            isProcessing = false;
        }
    });

    document.getElementById('retakePhoto').addEventListener('click', () => {
        canvas.style.display = 'none';
        document.getElementById('retakePhoto').style.display = 'none';
        document.getElementById('photoData').value = '';
        document.getElementById('photoStatus').style.display = 'block';
        document.getElementById('startCamera').disabled = false;
        photoTaken = false;
        isProcessing = false;
    });

    document.getElementById('confirmBtn').addEventListener('click', () => {
        if (isProcessing) {
            console.log('Already processing, ignoring click');
            return;
        }

        const photoRequired = (selectedType === 'clock_in_time') 
            ? {{ setting('photo_required_clock_in', 0) }} 
            : {{ setting('photo_required_clock_out', 0) }};

        console.log('Photo required:', photoRequired);
        console.log('Photo taken:', photoTaken);
        console.log('Photo data length:', document.getElementById('photoData').value.length);

        if (photoRequired && (!photoTaken || !document.getElementById('photoData').value)) {
            alert('Silakan ambil foto terlebih dahulu!');
            document.getElementById('photoStatus').style.display = 'block';
            return;
        }

        // Disable button to prevent double submission
        document.getElementById('confirmBtn').disabled = true;
        document.getElementById('confirmBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
        
        isProcessing = true;

        const form = document.getElementById('attendanceForm');

        // Clean up existing type inputs
        const existingTypeInputs = form.querySelectorAll('input[name="type"]');
        existingTypeInputs.forEach(input => input.remove());

        // Add type input
        let typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = selectedType;
        form.appendChild(typeInput);

        @if(setting('location_required') == '1')
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    // Clean up existing location inputs
                    const existingLocInputs = form.querySelectorAll('input[name="location_lat_long"]');
                    existingLocInputs.forEach(input => input.remove());
                    
                    let locInput = document.createElement('input');
                    locInput.type = 'hidden';
                    locInput.name = 'location_lat_long';
                    locInput.value = pos.coords.latitude + ',' + pos.coords.longitude;
                    form.appendChild(locInput);
                    
                    console.log('Submitting form with location');
                    form.submit();
                },
                error => {
                    console.error('Geolocation error:', error);
                    alert('Gagal mendapatkan lokasi. Coba lagi.');
                    resetSubmitButton();
                },
                {
                    timeout: 10000,
                    enableHighAccuracy: false
                }
            );
        } else {
            console.log('Geolocation not supported, submitting form');
            form.submit();
        }
        @else
        console.log('Submitting form without location');
        form.submit();
        @endif
    });

    function resetSubmitButton() {
        document.getElementById('confirmBtn').disabled = false;
        document.getElementById('confirmBtn').innerHTML = 'Konfirmasi';
        isProcessing = false;
    }

    // Clean up when modal is hidden
    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        if (stream) { 
            stream.getTracks().forEach(track => track.stop()); 
            stream = null; 
        }
        resetSubmitButton();
    });

@if(setting('location_required') == '1')
function initMap(){
    const mapDiv = document.getElementById('map');
    if(map){ 
        map.remove(); 
        map = null; 
    }
    mapDiv.innerHTML = '';
    
    setTimeout(() => {
        let lat = {{ $locations->first()?->lat_long ? explode(',',$locations->first()->lat_long)[0] : 0 }};
        let lng = {{ $locations->first()?->lat_long ? explode(',',$locations->first()->lat_long)[1] : 0 }};
        
        map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        @foreach($locations as $loc)
        @if($loc->is_active && $loc->lat_long)
        var coords = "{{ $loc->lat_long }}".split(',');
        L.circle([parseFloat(coords[0]), parseFloat(coords[1])], {
            color: 'green',
            fillColor: 'lightgreen',
            fillOpacity: 0.15,
            weight: 2,
            radius: {{ $loc->radius ?? 1 }} * 1000
        }).addTo(map).bindTooltip("{{ $loc->name }} (Radius: {{ $loc->radius ?? 1 }} km)");
        @endif
        @endforeach

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                let userLat = pos.coords.latitude;
                let userLng = pos.coords.longitude;
                
                L.marker([userLat, userLng], {
                    icon: L.icon({
                        iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-red.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    })
                }).addTo(map).bindPopup("📍 Lokasi Anda Saat Ini").openPopup();
                
                map.setView([userLat, userLng], 16);
            });
        }
    }, 100);
}
@endif
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnClockIn = document.getElementById('btnClockIn');
        const btnClockOut = document.getElementById('btnClockOut');
        const now = new Date();

        console.log('Current time:', now.toISOString());
        console.log('Current time local:', now.toString());

        function checkButton(btn, buttonName) {
            if (!btn) {
                console.log(`${buttonName} button not found`);
                return;
            }

            const startStr = btn.dataset.start;
            const endStr = btn.dataset.end;
            
            console.log(`${buttonName} window:`, {
                start: startStr,
                end: endStr
            });

            if (!startStr || !endStr) {
                console.log(`${buttonName}: Missing window data`);
                btn.disabled = true;
                btn.title = "Data jadwal tidak tersedia";
                btn.classList.add('btn-secondary');
                btn.classList.remove('btn-primary', 'btn-light');
                return;
            }

            const start = new Date(startStr);
            const end = new Date(endStr);

            const isInWindow = now >= start && now <= end;
            
            console.log(`${buttonName} check:`, {
                now: now.toISOString(),
                start: start.toISOString(),
                end: end.toISOString(),
                isInWindow: isInWindow,
                minutesToStart: Math.ceil((start - now) / (1000 * 60)),
                minutesFromEnd: Math.ceil((now - end) / (1000 * 60))
            });

            if (!isInWindow) {
                btn.disabled = true;
                
                if (now < start) {
                    const minutesUntilStart = Math.ceil((start - now) / (1000 * 60));
                    btn.title = `Belum waktunya. Bisa absen dalam ${minutesUntilStart} menit`;
                } else if (now > end) {
                    const minutesAfterEnd = Math.ceil((now - end) / (1000 * 60));
                    btn.title = `Waktu absensi sudah lewat ${minutesAfterEnd} menit yang lalu`;
                }
                
                btn.classList.add('btn-secondary');
                btn.classList.remove('btn-primary', 'btn-light');
            } else {
                console.log(`${buttonName} is enabled`);
                btn.disabled = false;
                btn.title = "Klik untuk melakukan absensi";
            }
        }

        checkButton(btnClockIn, 'Clock In');
        checkButton(btnClockOut, 'Clock Out');
    });
</script>

<!-- Leaflet & FontAwesome -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@endsection