{{-- Attendance Modal & Notification --}}
<style>
    /* Modal animation */
    .attendance-modal-content {
        transform: translateY(-10px);
        transition: transform 0.3s ease, opacity 0.3s ease, box-shadow 0.3s ease;
        border-radius: 1rem;
        background: linear-gradient(to top, rgba(0, 123, 255, 0.3), transparent);
        color: #fff;
        overflow: hidden;
    }

    .attendance-modal.show .attendance-modal-content {
        transform: translateY(0);
    }

    /* Card inside modal */
    .attendance-card {
        background: linear-gradient(to top, rgba(0, 123, 255, 0.1), transparent);
        color: #000;
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    /* Radius alert smooth */
    #attendanceRadiusAlert {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Tombol absen */
    #attendanceBtnNow {
        transition: all 0.3s ease;
    }

    /* Map rounded */
    #attendanceMap {
        border-radius: 12px;
    }
</style>

<!-- Notification Bell -->
<div class="position-relative d-inline-block m-1">
    <button type="button" class="btn btn-light border-0 bg-transparent position-relative p-0" style="outline: none;"
        data-bs-toggle="modal" data-bs-target="#attendanceLocationModal">
        <i class="bi text-light bi-geo-alt-fill fs-1"></i>
        <small class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            id="attendanceNotifCount">1</small>
    </button>
</div>

<!-- Attendance Modal -->
<div class="modal fade attendance-modal" id="attendanceLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content attendance-modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg- text-dark">
                <h5 class="modal-title"><i class="bi bi-geo-alt-fill"></i> Pemberitahuan Lokasi</h5>

                <!-- Tombol refresh lokasi -->
                <button type="button" class="btn btn-mb ms-3 btn-secondary text-light" id="attendanceRefreshBtn"
                    style="padding:0.25rem 0.5rem; border-radius:0.5rem;">
                    <i class="bi bi-arrow-clockwise"></i> <small> Refresh</small>
                </button>

                <!-- Tombol silang -->
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-2">
                <div class="card attendance-card border-0 shadow-sm rounded-4 p-3">
                    <!-- Map -->
                    <div id="attendanceMap" style="height:250px; overflow:hidden;" class="mb-3"></div>

                    <!-- Alamat -->
                    <label class="form-label fw-semibold">Alamat Anda</label>
                    <textarea id="attendanceAddress" rows="2" readonly
                        style="border:none; box-shadow:none; outline:none; resize:none;"
                        class="form-control form-control-sm rounded-3 mb-3">Menentukan alamat...</textarea>

                    <!-- Status radius -->
                    <div class="text-center py-2 rounded-3 mb-3 small" id="attendanceRadiusAlert">
                        Mendeteksi lokasi...
                    </div>

                    <!-- Tombol absen -->
                    <div class="text-center mb-2">
                        <a href="{{ route('attendance.create') }}" id="attendanceBtnNow" disabled
                            style="display:none; 
                            background: linear-gradient(90deg, #4facfe 0%, #005dfe 100%);
                            color: #fff; 
                            font-weight:600; 
                            border:none; 
                            border-radius:0.5rem;
                            padding:0.75rem 1.5rem;
                            box-shadow:0 4px 10px rgba(0,123,255,0.3);
                            text-decoration:none;">
                            Absen Sekarang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Latitude & Longitude -->
<input type="hidden" id="attendanceLatitude">
<input type="hidden" id="attendanceLongitude">

<!-- Leaflet & Bootstrap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mapEl = document.getElementById('attendanceMap');
        if (!mapEl) return; // jika halaman lain tidak ada modal ini

        const attendanceBtn = document.getElementById('attendanceBtnNow');
        const radiusAlert = document.getElementById('attendanceRadiusAlert');
        const addressInput = document.getElementById('attendanceAddress');
        const latInput = document.getElementById('attendanceLatitude');
        const lngInput = document.getElementById('attendanceLongitude');
        const refreshBtn = document.getElementById('attendanceRefreshBtn');
        const locationModal = document.getElementById('attendanceLocationModal');

        let map, userMarker, officeMarker, circle;
        const officeLat = {{ $office->latitude }};
        const officeLng = {{ $office->longitude }};
        const allowedRadius = {{ $office->radius }};

        locationModal.addEventListener('shown.bs.modal', function() {
            if (!map) {
                map = L.map('attendanceMap').setView([officeLat, officeLng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                officeMarker = L.marker([officeLat, officeLng]).addTo(map).bindPopup("Kantor")
                    .openPopup();
                circle = L.circle([officeLat, officeLng], {
                    color: 'blue',
                    fillColor: '#3f8efc',
                    fillOpacity: 0.2,
                    radius: allowedRadius
                }).addTo(map);
            }
            refreshLocation();
        });

        refreshBtn.addEventListener('click', refreshLocation);

        async function refreshLocation() {
            attendanceBtn.style.display = 'none';
            attendanceBtn.setAttribute('disabled', true);
            radiusAlert.className = 'alert alert-info text-center py-2 rounded-3 mb-3 small';
            radiusAlert.textContent = 'Mendeteksi lokasi...';
            addressInput.value = 'Menentukan alamat...';

            if (!navigator.geolocation) {
                radiusAlert.className = 'alert alert-warning text-center py-2 rounded-3 mb-3 small';
                radiusAlert.textContent = 'Browser tidak mendukung geolocation.';
                addressInput.value = 'Alamat tidak ditemukan';
                return;
            }

            navigator.geolocation.getCurrentPosition(async function(pos) {
                    const lat = parseFloat(pos.coords.latitude.toFixed(6));
                    const lng = parseFloat(pos.coords.longitude.toFixed(6));
                    latInput.value = lat;
                    lngInput.value = lng;

                    const distance = getDistanceFromLatLonInM(lat, lng, officeLat, officeLng);
                    updateStatus(distance);
                    attendanceBtn.style.display = 'inline-block';

                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 5000);
                        const res = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
                                signal: controller.signal
                            });
                        clearTimeout(timeoutId);
                        const data = await res.json();
                        addressInput.value = data.display_name || "Alamat tidak ditemukan";
                    } catch {
                        addressInput.value = "Alamat tidak ditemukan";
                    }

                    if (map) {
                        if (userMarker) userMarker.setLatLng([lat, lng]);
                        else userMarker = L.marker([lat, lng], {
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                                iconSize: [30, 30],
                                iconAnchor: [15, 30]
                            })
                        }).addTo(map).bindPopup("Lokasi Anda").openPopup();
                        const group = new L.featureGroup([officeMarker, userMarker]);
                        map.fitBounds(group.getBounds().pad(0.5));
                    }
                },
                function(err) {
                    radiusAlert.className =
                        'alert alert-warning text-center py-2 rounded-3 mb-3 small';
                    radiusAlert.textContent = 'Lokasi tidak terdeteksi.';
                    addressInput.value = 'Alamat tidak ditemukan';
                    attendanceBtn.style.display = 'none';
                    attendanceBtn.setAttribute('disabled', true);
                }, {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                });
        }

        function updateStatus(distance) {
            if (distance <= allowedRadius) {
                radiusAlert.className = 'alert alert-success text-center py-2 rounded-3 mb-3 small';
                radiusAlert.innerHTML =
                    `Anda di dalam radius dengan jarak <strong>${Math.round(distance)} M</strong> dari pusat radius.`;
                attendanceBtn.removeAttribute('disabled');
            } else {
                radiusAlert.className = 'alert alert-danger text-center py-2 rounded-3 mb-3 small';
                radiusAlert.textContent =
                    `Di luar radius (${Math.round(distance)} M, batas ${allowedRadius} M)`;
                attendanceBtn.setAttribute('disabled', true);
            }
        }

        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 *
                Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }
    });
</script>
