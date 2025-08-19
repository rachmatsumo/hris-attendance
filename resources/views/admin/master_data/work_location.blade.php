@extends('layouts.app')

@section('content')  

<div class="py-5 container">
  <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tombol Back di atas --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.index') }}" class="btn btn-link p-0">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <a class="btn btn-light openModalInputBtn" href="#modalInput" data-bs-toggle="modal" method="post" data-url="{{ route('location.store') }}" title="Tambah Area Kerja" data-id=""><i class="bi bi-plus"></i></a>               
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom py-2">
                        <h6>Area Kerja</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="v-middle">Nama Lokasi</th>
                                    <th class="v-middle">Koordinat</th>
                                    <th class="v-middle">Radius</th> 
                                    <th class="v-middle">Status</th> 
                                    <th class="v-middle">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($work_locations as $a)
                                <tr>
                                    <td class="v-middle">{{ $a->name }}</td>
                                    <td class="v-middle">{{ $a->lat_long }}</td>
                                    <td class="v-middle">{{ $a->radius }}km</td> 
                                    <td class="v-middle">{{ $a->status_name }}</td> 
                                    <td>
                                      <div class="d-flex">
                                        <button class="btn btn-sm btn-light openModalInputBtn editDataBtn me-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInput"
                                                method="put"
                                                title="Edit Area Kerja"
                                                data-id="{{ $a->id }}"
                                                data-url="{{ route('location.update', $a->id) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('location.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash2"></i></button>
                                        </form>
                                      </div>
                                    </td> 
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination flex-column justify-content-center mt-3"> 
                        {{ $work_locations->links('pagination::bootstrap-5') }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="" method="POST" id="inputForm">
        <!-- CSRF token jika Laravel -->
        @csrf
        <div id="methodField"></div>
        <div class="modal-header">
          <h5 class="modal-title" id="modalInputLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Nama Lokasi</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label for="lat_long" class="form-label">Latitude, Longitude</label>
                <input type="text" class="form-control" id="lat_long" name="lat_long" placeholder="-6.200000,106.816666" required readonly>
                <small class="text-muted">Klik pada peta untuk memilih koordinat</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="radius" class="form-label">Radius (km)</label>
                <input type="number" class="form-control" id="radius" name="radius" min="0.1" step="0.1" value="1" required>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <!-- Map Container -->
            <div id="map" style="height: 400px; border-radius: 8px;"></div>
          </div>

          <div class="mb-3"> 
            <label class="form-label" for="is_active">Status</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let marker;
let circle;
let isMapInitialized = false;

// Initialize map when modal is shown
document.getElementById('modalInput').addEventListener('shown.bs.modal', function () {
    if (!isMapInitialized) {
        initializeMap();
        isMapInitialized = true;
    } else {
        // Refresh map size if already initialized
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
});

function initializeMap() {
    // Default location (Jakarta)
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;
    
    // Initialize map
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add click event to map
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);
        
        // Update coordinate input
        document.getElementById('lat_long').value = `${lat},${lng}`;
        
        // Update marker position
        updateMarkerAndCircle(lat, lng, document.getElementById('radius').value);
    });
    
    // Add radius input change event
    document.getElementById('radius').addEventListener('input', function() {
        const coordinates = document.getElementById('lat_long').value;
        if (coordinates && marker) {
            const [lat, lng] = coordinates.split(',');
            updateMarkerAndCircle(lat, lng, this.value);
        }
    });
    
    // Set initial marker and circle if coordinates exist
    const coordinates = document.getElementById('lat_long').value;
    if (coordinates) {
        const [lat, lng] = coordinates.split(',');
        updateMarkerAndCircle(lat, lng, document.getElementById('radius').value);
        map.setView([lat, lng], 15);
    }
}

function updateMarkerAndCircle(lat, lng, radius) {
    // Remove existing marker and circle
    if (marker) {
        map.removeLayer(marker);
    }
    if (circle) {
        map.removeLayer(circle);
    }
    
    // Add new marker
    marker = L.marker([lat, lng]).addTo(map);
    
    // Add circle with radius (convert km to meters)
    circle = L.circle([lat, lng], {
        radius: radius * 1000,
        color: '#3388ff',
        fillColor: '#3388ff',
        fillOpacity: 0.2,
        weight: 2
    }).addTo(map);
    
    // Fit map to show the circle
    map.fitBounds(circle.getBounds(), { padding: [20, 20] });
}

// Handle add new location
document.addEventListener('click', function(e) {
    if (e.target.closest('.openModalInputBtn:not(.editDataBtn)')) {
        // Reset form for new entry
        document.getElementById('inputForm').reset();
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('inputForm').action = "{{ route('location.store') }}";
        document.getElementById('modalInputLabel').textContent = 'Tambah Area Kerja';
        document.getElementById('radius').value = '1';
        document.getElementById('lat_long').value = '';
        
        // Clear map markers
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        if (circle) {
            map.removeLayer(circle);
            circle = null;
        }
    }
});

// Handle edit location
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.editDataBtn');
    if(btn) {
        const id = btn.dataset.id;
        const url = `/location/${id}`;
        const form = document.getElementById('inputForm');
        const methodField = document.getElementById('methodField');

        fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            // Set action form dan method
            form.action = btn.dataset.url;
            methodField.innerHTML = '@method("PUT")';
            document.getElementById('modalInputLabel').textContent = 'Edit Area Kerja';

            // Fill form fields
            form.querySelector('#name').value = data.name;
            form.querySelector('#lat_long').value = data.lat_long;
            form.querySelector('#radius').value = data.radius; 
            form.querySelector('#is_active').value = data.is_active;

            // Update map with existing coordinates
            if (data.lat_long) {
                const [lat, lng] = data.lat_long.split(',');
                updateMarkerAndCircle(lat, lng, data.radius);
                if (map) {
                    map.setView([lat, lng], 15);
                }
            }
        })
        .catch(err => console.error(err));
    }
});

// Get user's current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude.toFixed(6);
            const lng = position.coords.longitude.toFixed(6);
            
            document.getElementById('lat_long').value = `${lat},${lng}`;
            updateMarkerAndCircle(lat, lng, document.getElementById('radius').value);
            map.setView([lat, lng], 15);
        }, function(error) {
            console.error("Error getting location:", error);
            alert("Tidak dapat mengakses lokasi. Pastikan izin lokasi sudah diberikan.");
        },
        {
                enableHighAccuracy: true,  
                timeout: 30000,            
                maximumAge: 0
        });
    } else {
        alert("Geolocation tidak didukung oleh browser ini.");
    }
}

// Add current location button
document.addEventListener('DOMContentLoaded', function() {
    // Add button after lat_long input
    const latLongDiv = document.querySelector('#lat_long').parentElement;
    const currentLocationBtn = document.createElement('button');
    currentLocationBtn.type = 'button';
    currentLocationBtn.className = 'btn btn-outline-primary btn-sm mt-2 float-right';
    currentLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Gunakan Lokasi Saya';
    currentLocationBtn.onclick = getCurrentLocation;
    latLongDiv.appendChild(currentLocationBtn);
});
</script>

@endsection