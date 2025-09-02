@extends('user.ly')

@section('content')
    <div class="container py-2 text-muted mb-5 pb-5">
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <div class="bg-white shadow rounded-4 p-3">
                    <h5 class="text-xs font-bold mb-3 text-center">Absensi Kehadiran</h5>

                    <!-- Alerts -->
                    @if (session('success'))
                        <div class="alert alert-success text-center rounded-lg">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-center rounded-lg">{{ session('error') }}</div>
                    @endif

                    <!-- Tambahkan di atas form -->
                    <!-- Tambahin ini di dalam body -->
                    <div id="popupOverlay"
                        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center;">
                        <div id="popupAlert"
                            style="background:white; padding:25px; border-radius:12px; max-width:400px; width:90%; text-align:center; font-size:16px; font-weight:bold;">
                        </div>
                    </div>


                    <form action="{{ route('attendance.checkin') }}" method="POST" enctype="multipart/form-data"
                        id="attendanceForm">
                        @csrf
                        <div class="row g-3">
                            <!-- Kolom 1 -->
                            <div class="m-0">
                                <input type="text" id="liveTime" name="time"
                                    class="form-control text-primary form-control-sm text-center"
                                    style="border: none; font-size: 1rem; font-weight: bold; background: transparent;"
                                    readonly>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Jenis Absensi -->
                                    <div class="mb-3 mt-3">
                                        <label class="form-label fw-semibold d-block mb-2">Jenis Absensi</label>
                                        <div class="d-flex">
                                            <input type="radio" class="btn-check" name="type" id="checkIn"
                                                value="in" autocomplete="off" required>
                                            <label for="checkIn"
                                                style="
            flex:1;
            text-align:center;
            padding:0.6rem 1rem;
            border:1px solid #007bff;
            border-right:none; /* hilangkan border kanan */
            border-radius:1rem 0 0 1rem; /* ujung kiri bulat */
            background:transparent;
            color:#007bff;
            font-weight:600;
            cursor:pointer;
            transition: all 0.3s ease;
        ">
                                                Masuk
                                            </label>

                                            <input type="radio" class="btn-check" name="type" id="checkOut"
                                                value="out" autocomplete="off" required>
                                            <label for="checkOut"
                                                style="
            flex:1;
            text-align:center;
            padding:0.6rem 1rem;
            border:1px solid #007bff;
            border-radius:0 1rem 1rem 0; /* ujung kanan bulat */
            background:transparent;
            color:#007bff;
            font-weight:600;
            cursor:pointer;
            transition: all 0.3s ease;
        ">
                                                Pulang
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Map & Alamat -->
                                    <div class="mb-3">


                                        <div id="map" style="height:180px;border-radius:12px;overflow:hidden;"
                                            class="mb-2"></div>
                                        <textarea id="address" name="address" rows="2" readonly
                                            style="border:none; box-shadow:none; outline:none; resize:none;" class="form-control form-control-sm rounded-lg">Menentukan alamat anda...</textarea>
                                        <div class="alert text-center py-1 rounded-lg mb-2 small" id="radiusAlert">
                                            Mendeteksi
                                            lokasi anda...</div>
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>Lat: <span id="latText">-</span></span>
                                            <span>Lng: <span id="lngText">-</span></span>
                                        </div>
                                        <input type="hidden" name="latitude" id="latitude">
                                        <input type="hidden" name="longitude" id="longitude">
                                    </div>
                                </div>

                                <!-- Kolom 2: Selfie -->
                                <div class="border rounded-lg overflow-hidden mb-2"
                                    style="position: relative; height: 280px;">
                                    <video id="video" autoplay playsinline class="w-100 h-100 object-fit-cover"></video>
                                    <button type="button" id="captureBtn"
                                        class="btn btn-sm btn-primary position-absolute bottom-0 end-0 m-2 d-flex align-items-center justify-content-center"
                                        style="width:40px; height:40px; border-radius:50%;">
                                        <i class="fas fa-camera"></i>
                                    </button>

                                </div>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <input type="hidden" name="photo" id="selfie">
                                <div id="preview" class="text-center small"></div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="mt-3">
                                <button type="submit" id="submitBtn"
                                    class="btn btn-success w-100 py-2 rounded-lg fw-semibold" disabled>
                                    Kirim Absensi
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radiusAlert = document.getElementById('radiusAlert');
            const addressInput = document.getElementById('address');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const submitBtn = document.getElementById('submitBtn');
            const captureBtn = document.getElementById('captureBtn');
            const selfieInput = document.getElementById('selfie');
            const liveTime = document.getElementById('liveTime');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('preview');

            const overlay = document.getElementById('popupOverlay');
            const popup = document.getElementById('popupAlert');

            const offices = @json($offices);

            function showPopup(message, type = "info") {
                popup.style.color = (type === "success") ? "green" : (type === "danger" ? "red" : "#333");
                popup.innerHTML = message;
                overlay.style.display = "flex";
            }

            function hidePopup() {
                overlay.style.display = "none";
            }

            // Map
            let map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            }).setView([0, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: false
            }).addTo(map);

            const officeMarkers = [];
            const officeCircles = [];

            offices.forEach(office => {
                const lat = parseFloat(office.latitude);
                const lng = parseFloat(office.longitude);
                const radius = office.radius || 0;

                const circle = L.circle([lat, lng], {
                    color: '#28a745',
                    weight: 1.2,
                    fillColor: '#8fd19e',
                    fillOpacity: 0.1,
                    radius: radius
                });
                officeCircles.push(circle);

                const marker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
                        iconSize: [18, 18],
                        iconAnchor: [9, 18]
                    })
                }).bindPopup(`<strong>${office.name}</strong><br>Radius: ${radius} m`);
                officeMarkers.push(marker);
            });

            officeMarkers.forEach(m => m.addTo(map));
            const officeCircleGroup = L.layerGroup(officeCircles).addTo(map);
            const group = new L.featureGroup([...officeMarkers, ...officeCircles]);
            map.fitBounds(group.getBounds().pad(0.2));

            let userMarker;

            async function refreshLocation() {
                radiusAlert.className = 'alert alert-info text-center py-1 rounded-lg mb-2 small';
                radiusAlert.textContent = 'Mendeteksi lokasi...';
                addressInput.value = 'Menentukan alamat...';
                showPopup("Sedang mencari lokasi, harap tunggu...", "info");

                if (!navigator.geolocation) {
                    radiusAlert.className = 'alert alert-warning text-center py-1 rounded-lg mb-2 small';
                    radiusAlert.textContent = 'Browser tidak mendukung geolocation.';
                    addressInput.value = 'Alamat tidak ditemukan';
                    showPopup("Browser tidak mendukung geolocation ❌", "danger");
                    setTimeout(hidePopup, 2000);
                    return;
                }


                navigator.geolocation.getCurrentPosition(async function(pos) {
                        const lat = parseFloat(pos.coords.latitude.toFixed(6));
                        const lng = parseFloat(pos.coords.longitude.toFixed(6));
                        latInput.value = lat;
                        lngInput.value = lng;

                        // Update teks di UI
                        document.getElementById('latText').textContent = lat;
                        document.getElementById('lngText').textContent = lng;

                        const officesWithin = offices.map(office => {
                            const distance = getDistanceFromLatLonInM(lat, lng, parseFloat(
                                office.latitude), parseFloat(office.longitude));
                            return {
                                ...office,
                                distance
                            };
                        }).filter(o => o.distance <= o.radius);

                        const isInside = officesWithin.length > 0;
                        updateStatus(isInside);

                        // Reverse geocoding
                        try {
                            const controller = new AbortController();
                            const timeoutId = setTimeout(() => controller.abort(), 4000);
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

                        // Marker user
                        const userIcon = L.divIcon({
                            html: '<i class="fa fa-user fa-lg" style="color:#007bff;"></i>',
                            className: '',
                            iconSize: [18, 18],
                            iconAnchor: [9, 18]
                        });
                        if (userMarker) userMarker.setLatLng([lat, lng]);
                        else userMarker = L.marker([lat, lng], {
                            icon: userIcon
                        }).addTo(map);

                        // Hide circle saat zoom
                        officeCircleGroup.remove();

                        // Smooth zoom ke user
                        map.flyTo([lat, lng], 16, {
                            animate: true,
                            duration: 2
                        });

                        setTimeout(() => {
                            officeCircleGroup.addTo(map);

                            if (isInside) {
                                showPopup(
                                    "Lokasi ditemukan ✅, Anda di dalam radius, silahkan absen",
                                    "success");
                                setTimeout(hidePopup, 1000);
                                submitBtn.disabled = false;
                            } else {
                                showPopup(
                                    "Lokasi ditemukan ❌, Anda di luar radius, Anda tidak bisa absen",
                                    "danger");
                                setTimeout(hidePopup, 2000);
                                submitBtn.disabled = true;
                            }
                        }, 2100);

                    },
                    function() {
                        radiusAlert.className =
                            'alert alert-warning text-center py-1 rounded-lg mb-2 small';
                        radiusAlert.textContent = 'Lokasi tidak terdeteksi.';
                        addressInput.value = 'Alamat tidak ditemukan';
                        showPopup("Lokasi tidak ditemukan ❌", "danger");
                        setTimeout(hidePopup, 2000);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
            }

            function updateStatus(isInside) {
                if (isInside) {
                    radiusAlert.className = 'alert alert-success text-center py-1 rounded-lg mb-2 small';
                    radiusAlert.innerHTML = `✅ Dalam radius `;
                } else {
                    radiusAlert.className = 'alert alert-danger text-center py-1 rounded-lg mb-2 small';
                    radiusAlert.textContent = '❌ Di luar radius';
                }
            }

            function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI /
                    180) * Math.sin(dLon / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            refreshLocation();

            // Kamera selfie
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(stream => video.srcObject = stream)
                    .catch(() => alert("Tidak bisa mengakses kamera!"));
            }

            captureBtn.addEventListener('click', function() {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                context.fillStyle = "rgba(0,0,0,0.5)";
                context.fillRect(0, canvas.height - 50, canvas.width, 50);
                context.fillStyle = "#fff";
                context.font = "20px Arial";
                context.fillText(liveTime.value, 10, canvas.height - 30);
                context.fillText(addressInput.value.substring(0, 40) + "...", 10, canvas.height - 10);
                const dataURL = canvas.toDataURL('image/png');
                selfieInput.value = dataURL;
                preview.innerHTML =
                    `<img src="${dataURL}" class="rounded shadow mt-2" style="width:100%;max-width:200px;" alt="Preview Selfie">`;

                const distance = getDistanceFromLatLonInM(parseFloat(latInput.value), parseFloat(lngInput
                    .value), userMarker.getLatLng().lat, userMarker.getLatLng().lng);
                submitBtn.disabled = distance > 200;
            });

            // Live clock
            setInterval(() => {
                const now = new Date();
                liveTime.value =
                    `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
            }, 1000);

        });
    </script>





    <script>
        // Menambahkan efek klik/hover inline tanpa CSS tambahan
        const labels = document.querySelectorAll('.btn-check + label');
        labels.forEach(label => {
            const input = document.getElementById(label.htmlFor);
            label.addEventListener('mouseover', () => {
                if (!input.checked) {
                    label.style.background = 'linear-gradient(135deg, #6fb1fc, #4364f7)';
                    label.style.color = '#fff';
                }
            });
            label.addEventListener('mouseout', () => {
                if (!input.checked) {
                    label.style.background = 'transparent';
                    label.style.color = '#007bff';
                }
            });
            input.addEventListener('change', () => {
                labels.forEach(l => {
                    const inp = document.getElementById(l.htmlFor);
                    if (inp.checked) {
                        l.style.background = 'linear-gradient(135deg, #6fb1fc, #4364f7)';
                        l.style.color = '#fff';
                    } else {
                        l.style.background = 'transparent';
                        l.style.color = '#007bff';
                    }
                });
            });
        });
    </script>
@endsection
