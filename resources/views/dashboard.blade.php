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

        <div class="row g-4">
            <div class="col-12 col-md-4">
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
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Tinggi Sampah</h5>
                        <h2 class="text-primary" id="tinggi">-</h2>
                        <div class="progress">
                            <div class="progress-bar bg-primary" id="tinggi-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Lokasi</h5>
                        <p id="location">Lat: -<br>Lng: -</p>
                        <div id="map" class="w-100" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow-sm">
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

    <!-- Leaflet dan Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <script>
        let map = L.map('map').setView([-6.9147, 107.6098], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let markers = {};
        let selectedDeviceId = '';

        function fetchData() {
            axios.get('/api/dashboard-data')
                .then(res => {
                    const allData = res.data;
                    const tbody = document.getElementById('data-body');
                    tbody.innerHTML = '';

                    let latest = null;
                    let bounds = [];

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

                        if (!markers[item.device_id]) {
                            const marker = L.marker([item.latitude, item.longitude])
                                .addTo(map)
                                .bindPopup(`<b>${item.device_id}</b>`)
                                .on('click', function () {
                                    selectedDeviceId = item.device_id;
                                    document.getElementById('device-select').value = item.device_id;
                                    fetchData();
                                });
                            markers[item.device_id] = marker;
                        } else {
                            markers[item.device_id].setLatLng([item.latitude, item.longitude]);
                        }

                        bounds.push([item.latitude, item.longitude]);
                    });

                    if (tbody.innerHTML.trim() === '') {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data sensor</td>
                            </tr>
                        `;
                    }

                    if (latest) {
                        updateDashboard(latest);
                    } else {
                        updateDashboardEmpty();
                    }

                    if (!selectedDeviceId && bounds.length) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                    } else if (latest) {
                        map.setView([latest.latitude, latest.longitude], 16);
                    }
                });
        }

        function updateDashboard(data) {
            document.getElementById('berat').textContent = `${data.berat.toFixed(2)} gram`;
            document.getElementById('tinggi').textContent = `${data.tinggi} cm`;
            document.getElementById('location').innerHTML = `Lat: ${data.latitude}<br>Lng: ${data.longitude}`;

            document.getElementById('berat-progress').style.width = `${Math.min((data.berat / 100) * 100, 100)}%`;
            document.getElementById('tinggi-progress').style.width = `${Math.min((data.tinggi / 100) * 100, 100)}%`;
        }

        function updateDashboardEmpty() {
            document.getElementById('berat').textContent = '-';
            document.getElementById('tinggi').textContent = '-';
            document.getElementById('location').innerHTML = 'Lat: -<br>Lng: -';
            document.getElementById('berat-progress').style.width = '0%';
            document.getElementById('tinggi-progress').style.width = '0%';
        }

        document.getElementById('device-select').addEventListener('change', function (e) {
            selectedDeviceId = e.target.value;
            fetchData();
        });

        fetchData();
        setInterval(fetchData, 3000);
    </script>
</x-app-layout>
