<x-app-layout>
    <div class="container-fluid py-4 px-3">
        <h1 class="fw-bold mb-4 text-center text-md-start">Dashboard Monitoring Tempat Sampah Pintar</h1>

        <div class="mb-3">
            <label for="device-select" class="form-label fw-bold">Pilih Device:</label>
            <select id="device-select" class="form-select form-select-sm">
                <option value="">Semua Device</option>
                @foreach ($devices as $device)
                    <option value="{{ $device->device_id }}">{{ $device->device_id }}</option>
                @endforeach
            </select>
        </div>

        <!-- Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-bucket-fill text-success me-2"></i>Berat Sampah</h5>
                        <h2 class="text-success" id="berat">-</h2>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="berat-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Tinggi Sampah</h5>
                        <h2 class="text-primary" id="tinggi">-</h2>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="tinggi-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Lokasi</h5>
                        <p id="location">Lat: -<br>Lng: -</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold">Peta Lokasi & Rute Tempat Sampah</h5>
                <div id="map" style="height: 500px;" class="rounded shadow-sm"></div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Data Realtime</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Berat (gram)</th>
                                <th>Tinggi (cm)</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="data-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <script>
        let map = L.map('map').setView([-6.9147, 107.6098], 13);
        let markers = {};
        let routeControl;
        let selectedDeviceId = '';

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        navigator.geolocation.getCurrentPosition(pos => {
            const userLatLng = [pos.coords.latitude, pos.coords.longitude];
            const userMarker = L.marker(userLatLng, {
                icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [30, 30]
                })
            }).addTo(map).bindPopup("Lokasi Anda");

            fetchData(userLatLng);
        }, err => {
            alert("Gagal mendapatkan lokasi user");
            fetchData(null);
        });

        function fetchData(userLatLng = null) {
            axios.get('/api/dashboard-data')
                .then(res => {
                    const allData = res.data;
                    const tbody = document.getElementById('data-body');
                    tbody.innerHTML = '';

                    let latest = null;
                    let routeWaypoints = [];

                    allData.forEach(item => {
                        if (selectedDeviceId && item.device_id !== selectedDeviceId) return;

                        tbody.innerHTML += `
                            <tr>
                                <td>${item.device_id}</td>
                                <td>${item.berat.toFixed(2)}</td>
                                <td>${item.tinggi}</td>
                                <td>${item.latitude}</td>
                                <td>${item.longitude}</td>
                                <td>${item.created_at}</td>
                            </tr>
                        `;

                        if (!latest || new Date(item.created_at) > new Date(latest.created_at)) {
                            latest = item;
                        }

                        // Marker
                        if (!markers[item.device_id]) {
                            markers[item.device_id] = L.marker([item.latitude, item.longitude])
                                .addTo(map)
                                .bindPopup(`<b>${item.device_id}</b>`)
                                .on('click', () => {
                                    selectedDeviceId = item.device_id;
                                    document.getElementById('device-select').value = item.device_id;
                                    fetchData(userLatLng);
                                });
                        } else {
                            markers[item.device_id].setLatLng([item.latitude, item.longitude]);
                        }

                        routeWaypoints.push(L.latLng(item.latitude, item.longitude));
                    });

                    if (latest) updateDashboard(latest);
                    else updateDashboardEmpty();

                    if (userLatLng && routeWaypoints.length > 0) {
                        const allPoints = [L.latLng(userLatLng), ...routeWaypoints];

                        if (routeControl) {
                            map.removeControl(routeControl);
                        }

                        routeControl = L.Routing.control({
                            waypoints: allPoints,
                            routeWhileDragging: false,
                            show: false,
                            addWaypoints: false,
                            createMarker: () => null
                        }).addTo(map);
                    }
                });
        }

        function updateDashboard(data) {
            // Berat
            document.getElementById('berat').textContent = `${data.berat.toFixed(2)} gram`;
            document.getElementById('berat-progress').style.width = `${Math.min(data.berat, 100)}%`;

            // Tinggi (as percent)
            const tinggiMax = 100;
            let tinggiPersen = Math.min(Math.round((data.tinggi / tinggiMax) * 100), 100);
            document.getElementById('tinggi').textContent = `${tinggiPersen}%`;

            const tinggiBar = document.getElementById('tinggi-progress');
            tinggiBar.style.width = `${tinggiPersen}%`;
            tinggiBar.classList.remove('bg-success', 'bg-warning', 'bg-danger');

            if (tinggiPersen > 80) {
                tinggiBar.classList.add('bg-danger');
            } else if (tinggiPersen > 60) {
                tinggiBar.classList.add('bg-warning');
            } else {
                tinggiBar.classList.add('bg-success');
            }

            // Lokasi
            document.getElementById('location').innerHTML = `Lat: ${data.latitude}<br>Lng: ${data.longitude}`;
        }

        function updateDashboardEmpty() {
            document.getElementById('berat').textContent = '-';
            document.getElementById('tinggi').textContent = '-';
            document.getElementById('location').innerHTML = 'Lat: -<br>Lng: -';
            document.getElementById('berat-progress').style.width = '0%';
            const tinggiBar = document.getElementById('tinggi-progress');
            tinggiBar.style.width = '0%';
            tinggiBar.classList.remove('bg-success', 'bg-warning', 'bg-danger');
        }

        document.getElementById('device-select').addEventListener('change', function (e) {
            selectedDeviceId = e.target.value;
            navigator.geolocation.getCurrentPosition(pos => {
                fetchData([pos.coords.latitude, pos.coords.longitude]);
            });
        });

        setInterval(() => {
            navigator.geolocation.getCurrentPosition(pos => {
                fetchData([pos.coords.latitude, pos.coords.longitude]);
            });
        }, 5000);
    </script>
</x-app-layout>
