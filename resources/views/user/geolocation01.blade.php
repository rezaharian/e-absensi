<!-- Div untuk peta, kirim data office lewat data-offices -->
<div id="attendanceMapContainer" data-offices='@json($office)' class="row text-muted">
    <div class="col-6">
        <div id="attendanceMap"
            style="height: 120px; border-radius: .5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border:1px solid rgba(0,0,0,0.041);">
        </div>
    </div>
    <div class="col-6">
        <div class="attendance-info">
            <textarea id="attendanceAddress" readonly
                style="font-size:.75rem; border:1px solid #eee; border-radius:.5rem; background:#f9f9f9; resize:none; height:65px; max-width: 100%; padding:.3rem .4rem; line-height:1.2; color:#6c757d;">
Menentukan alamat...
            </textarea>
            <div id="attendanceRadiusAlert" class="text-center small bg-light"
                style="font-size:.75rem; border-radius:.5rem; padding:.25rem .5rem;">
                Mendeteksi lokasi...
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="attendanceLatitude">
<input type="hidden" id="attendanceLongitude">

<!-- Leaflet & FontAwesome -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const radiusAlert = document.getElementById('attendanceRadiusAlert');
        const addressInput = document.getElementById('attendanceAddress');
        const latInput = document.getElementById('attendanceLatitude');
        const lngInput = document.getElementById('attendanceLongitude');

        let map, userMarker;

        const offices = @json($offices);

        // Inisialisasi map
        map = L.map('attendanceMap', {
                zoomControl: false,
                attributionControl: false
            })
            .setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const officeMarkers = [];
        const circles = [];

        // Marker dan circle untuk semua kantor
        offices.forEach(office => {
            const lat = parseFloat(office.latitude);
            const lng = parseFloat(office.longitude);
            const radius = office.radius;

            const circle = L.circle([lat, lng], {
                color: '#28a745',
                weight: 1.2,
                fillColor: '#8fd19e',
                fillOpacity: 0.1, // tipis agar tidak terlalu mencolok
                radius: radius
            });
            circles.push(circle);

            const marker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: "https://cdn-icons-png.flaticon.com/512/684/684908.png",
                    iconSize: [18, 18],
                    iconAnchor: [9, 18]
                })
            }).bindPopup(`<strong>${office.name}</strong><br>Radius: ${radius} m`);
            officeMarkers.push(marker);
        });

        // Tambahkan marker dan circle ke map
        officeMarkers.forEach(m => m.addTo(map));
        const officeCircleGroup = L.layerGroup(circles).addTo(map);

        // Zoom awal semua kantor
        const initialGroup = new L.featureGroup([...officeMarkers, ...circles]);
        map.fitBounds(initialGroup.getBounds().pad(0.2));

        async function refreshLocation() {
            // Set default awal
            radiusAlert.className = 'alert alert-info text-center py-1 rounded-2 mb-2 small';
            radiusAlert.textContent = 'Mendeteksi lokasi...';
            addressInput.value = 'Menentukan alamat...';

            if (!navigator.geolocation) {
                radiusAlert.className = 'alert alert-warning text-center py-1 rounded-2 mb-2 small';
                radiusAlert.textContent = 'Browser tidak mendukung geolocation.';
                addressInput.value = 'Alamat tidak ditemukan';
                return;
            }

            navigator.geolocation.getCurrentPosition(async function(pos) {
                    const lat = parseFloat(pos.coords.latitude.toFixed(6));
                    const lng = parseFloat(pos.coords.longitude.toFixed(6));
                    latInput.value = lat;
                    lngInput.value = lng;

                    // Hitung jarak ke semua kantor
                    const officesWithin = offices
                        .map(office => {
                            const distance = getDistanceFromLatLonInM(lat, lng, parseFloat(
                                office.latitude), parseFloat(office.longitude));
                            return {
                                ...office,
                                distance
                            };
                        })
                        .filter(o => o.distance <= o.radius);

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
                        html: '<i class="fa fa-user fa-lg" style="color: #007bff;"></i>',
                        className: '',
                        iconSize: [18, 18],
                        iconAnchor: [9, 18]
                    });

                    if (userMarker) {
                        userMarker.setLatLng([lat, lng]);
                    } else {
                        userMarker = L.marker([lat, lng], {
                            icon: userIcon
                        }).addTo(map);
                    }

                    // *** Sembunyikan circle kantor sementara saat zoom user ***
                    officeCircleGroup.remove();

                    // Smooth zoom ke user
                    map.flyTo([lat, lng], 16, {
                        animate: true,
                        duration: 2
                    });

                    // Setelah zoom selesai, tampilkan circle kantor lagi
                    setTimeout(() => {
                        officeCircleGroup.addTo(map);
                    }, 2100); // sedikit lebih lama dari duration flyTo

                },
                function() {
                    radiusAlert.className =
                        'alert alert-warning text-center py-1 rounded-2 mb-2 small';
                    radiusAlert.textContent = 'Lokasi tidak terdeteksi.';
                    addressInput.value = 'Alamat tidak ditemukan';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
        }

        refreshLocation();

        function updateStatus(isInside) {
            if (isInside) {
                radiusAlert.className = 'alert alert-success text-center py-1 rounded-2 mb-2 small';
                radiusAlert.innerHTML = `✅ Dalam radius `;
            } else {
                radiusAlert.className = 'alert alert-danger text-center py-1 rounded-2 mb-2 small';
                radiusAlert.textContent = '❌ Di luar radius ';
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
    });
</script>
