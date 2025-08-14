@extends('layouts.app')

@section('content') 

<div class="row">
    <div class="col-md-4 col-lg-3 col-12 bg-gradient-blue py-4"> 
        
        <form id="attendanceForm" action="{{ route('attendances.store') }}" method="POST" class="card p-4" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="photoData" name="photo" required>
            
            <div class="row w-100 border-bottom mb-2">
                <div class="col-12 d-flex justify-content-between mb-3">
                    <div><b>Jadwal Kerja</b></div>
                    <div class="text-end">{{ now()->locale('id')->translatedFormat('D, d M Y') }}</div>
                </div>
                <div class="mb-3 text-center">
                    <h1>
                        {{ optional($schedule)->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '-' }}
                        -
                        {{ optional($schedule)->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '-' }}
                    </h1>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between mt-2">
                <button type="button" class="btn btn-lg btn-primary me-2 rounded-1 w-50" onclick="openConfirmModal('clock_in_time')">Masuk</button>
                <button type="button" class="btn btn-lg btn-light ms-2 rounded-1 w-50" onclick="openConfirmModal('clock_out_time')">Pulang</button>
            </div>
        </form>

    </div>
    
    <div class="col-md-8 col-lg-9 col-12 py-4">
        <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
            <h6>Aktivitas</h6>
            <a class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 " href="#">Log absensi</a>
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
                        <td class="v-middle">{{ optional($a->workSchedule)->start_time ? \Carbon\Carbon::parse($a->workSchedule->start_time)->format('H:i') : '-' }} - {{ optional($a->workSchedule)->end_time ? \Carbon\Carbon::parse($a->workSchedule->end_time)->format('H:i') : '-' }}</td>
                        <td class="v-middle">{{ $a->clock_in_time ? $a->clock_in_time->format('H:i:s') : '-' }}</td>
                        <td class="v-middle">{{ $a->clock_out_time ? $a->clock_out_time->format('H:i:s') : '-' }}</td>
                        <td class="v-middle">{{ ucfirst($a->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

                @if($schedule->is_location_limited)
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

                    <div class="camera-container mb-3" style="position: relative; max-width: 400px;">
                        <video id="video" width="100%" height="300" style="border: 2px solid #dee2e6; border-radius: 8px; display: none;" autoplay muted></video>
                        <canvas id="canvas" width="400" height="300" style="border: 2px solid #dee2e6; border-radius: 8px; display: none;"></canvas>
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

    function openConfirmModal(type) {
        selectedType = type;
        document.getElementById('modalText').textContent = 
            (type === 'clock_in_time') ? 'Apakah Anda yakin ingin melakukan clock in?' : 'Apakah Anda yakin ingin melakukan clock out?';

        resetCamera();

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();

        @if($schedule->is_location_limited)
        document.getElementById('confirmModal').addEventListener('shown.bs.modal', function () {
            initMap();
        }, { once: true });
        @endif
    }

    // Camera Functions
    function resetCamera() {
        photoTaken = false;
        document.getElementById('photoData').value = '';
        document.getElementById('photoStatus').style.display = 'block';
        document.getElementById('startCamera').disabled = false;
        document.getElementById('capturePhoto').disabled = true;
        document.getElementById('retakePhoto').style.display = 'none';
        document.getElementById('video').style.display = 'none';
        document.getElementById('canvas').style.display = 'none';
        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
    }

    document.getElementById('startCamera').addEventListener('click', async () => {
        document.getElementById('cameraLoading').style.display = 'block';
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 400, height: 300 } });
            video = document.getElementById('video');
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                document.getElementById('cameraLoading').style.display = 'none';
                video.style.display = 'block';
                document.getElementById('startCamera').disabled = true;
                document.getElementById('capturePhoto').disabled = false;
            };
        } catch (err) {
            document.getElementById('cameraLoading').style.display = 'none';
            alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.');
            console.error(err);
        }
    });

    document.getElementById('capturePhoto').addEventListener('click', () => {
        canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        canvas.width = video.clientWidth;
        canvas.height = video.clientHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoDataUrl = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('photoData').value = photoDataUrl;

        video.style.display = 'none';
        canvas.style.display = 'block';
        document.getElementById('capturePhoto').disabled = true;
        document.getElementById('retakePhoto').style.display = 'inline-block';
        document.getElementById('photoStatus').style.display = 'none';
        photoTaken = true;

        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
    });

    document.getElementById('retakePhoto').addEventListener('click', () => {
        canvas.style.display = 'none';
        document.getElementById('retakePhoto').style.display = 'none';
        document.getElementById('photoData').value = '';
        document.getElementById('photoStatus').style.display = 'block';
        document.getElementById('startCamera').disabled = false;
        photoTaken = false;
    });

    document.getElementById('confirmBtn').addEventListener('click', () => {
        if (!photoTaken || !document.getElementById('photoData').value) {
            alert('Silakan ambil foto terlebih dahulu!');
            document.getElementById('photoStatus').style.display = 'block';
            return;
        }

        const form = document.getElementById('attendanceForm');

        // input type
        let typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'type';
        typeInput.value = selectedType;
        form.appendChild(typeInput);

        @if($schedule->is_location_limited)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                let locInput = document.createElement('input');
                locInput.type = 'hidden';
                locInput.name = 'location_lat_long';
                locInput.value = pos.coords.latitude + ',' + pos.coords.longitude;
                form.appendChild(locInput);
                form.submit();
            });
        } else {
            form.submit();
        }
        @else
        form.submit();
        @endif
    });

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        if (stream) { 
            stream.getTracks().forEach(track => track.stop()); 
            stream = null; 
        }
    });

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal',()=>{
        if(stream){ 
            stream.getTracks().forEach(t=>t.stop()); 
            stream=null; 
        }
    });

@if($schedule->is_location_limited)
function initMap(){
    const mapDiv=document.getElementById('map');
    if(map){ map.remove(); map=null; }
    mapDiv.innerHTML='';
    setTimeout(()=>{
        let lat={{ $locations->first()?->lat_long ? explode(',',$locations->first()->lat_long)[0] : 0 }};
        let lng={{ $locations->first()?->lat_long ? explode(',',$locations->first()->lat_long)[1] : 0 }};
        map=L.map('map').setView([lat,lng],15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

        @foreach($locations as $loc)
        @if($loc->is_active && $loc->lat_long)
        var c="{{ $loc->lat_long }}".split(','); L.circle([parseFloat(c[0]),parseFloat(c[1])],{color:'green',fillColor:'lightgreen',fillOpacity:0.15,weight:2,radius:{{ $loc->radius??1 }}*1000}).addTo(map).bindTooltip("{{ $loc->name }} (Radius: {{ $loc->radius??1 }} km)");
        @endif
        @endforeach

        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(pos=>{
                let uLat=pos.coords.latitude, uLng=pos.coords.longitude;
                L.marker([uLat,uLng],{icon:L.icon({iconUrl:'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@master/img/marker-icon-red.png',iconSize:[25,41],iconAnchor:[12,41],popupAnchor:[1,-34]})}).addTo(map).bindPopup("📍 Lokasi Anda Saat Ini").openPopup();
                map.setView([uLat,uLng],16);
            });
        }
    },100);
}
@endif
</script>


<!-- Leaflet & FontAwesome -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


@endsection
