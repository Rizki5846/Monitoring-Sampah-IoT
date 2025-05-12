<x-app-layout>
    <div class="container-fluid py-4 px-3">
        <h1 class="fw-bold mb-4 text-center text-md-start">Dashboard Monitoring Tempat Sampah Pintar</h1>

        <div class="mb-3">
            <label for="device-select" class="form-label fw-bold">Pilih Device:</label>
            <select id="device-select" class="form-select form-select-sm">
                <option value="">Semua Device</option>
            </select>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-bucket-fill text-success me-2"></i>Berat Sampah
                        </h5>
                        <h2 class="text-success" id="berat">0 gram</h2>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="berat-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>Tinggi Sampah
                        </h5>
                        <h2 class="text-primary" id="tinggi">0 cm</h2>
                        <div class="progress">
                            <div class="progress-bar bg-primary" id="tinggi-progress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Lokasi
                        </h5>
                        <p id="location">Lat: -<br>Lng: -</p>
                        <div id="map" class="w-100" style="height: 200px; min-height: 200px;"></div>
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

    <!-- External Script -->
    <script src="https://cdn.jsdelivr.net/npm/mqtt/dist/mqtt.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom Script (Same as yours) -->
    <script>
        const client = mqtt.connect('wss://fedf34b846cf43389053a83eadaf35c1.s1.eu.hivemq.cloud:8884/mqtt', {
            username: 'hivemq.webclient.1746850271989',
            password: 't>K#AJ89i1@%0yHjeqVU'
        });

        let map = L.map('map').setView([-6.914744, 107.609810], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let devices = {};
        let selectedDeviceId = '';

        client.on('connect', function () {
            console.log('Connected to MQTT');
            client.subscribe('iot/tempatsampah');
        });

        client.on('message', function (topic, message) {
            const data = JSON.parse(message.toString());
            let deviceId = data.device_id || 'Unknown Device';
            let waktu = new Date().toLocaleString();

            if (!devices[deviceId]) {
                let option = document.createElement('option');
                option.value = deviceId;
                option.text = deviceId;
                document.getElementById('device-select').appendChild(option);

                devices[deviceId] = {
                    marker: L.marker([data.latitude, data.longitude], {title: deviceId}).addTo(map),
                    path: L.polyline([], {color: getRandomColor()}).addTo(map),
                    coordinates: []
                };

                devices[deviceId].marker.bindPopup(`<b>${deviceId}</b>`);
                devices[deviceId].marker.on('click', () => {
                    map.setView([data.latitude, data.longitude], 17);
                    document.getElementById('device-select').value = deviceId;
                    selectedDeviceId = deviceId;
                    refreshTable();
                });
            }

            devices[deviceId].marker.setLatLng([data.latitude, data.longitude]);
            devices[deviceId].coordinates.push([data.latitude, data.longitude]);
            devices[deviceId].path.setLatLngs(devices[deviceId].coordinates);

            if (!devices[deviceId].data) devices[deviceId].data = [];
            devices[deviceId].data.unshift({
                berat: data.berat,
                tinggi: data.tinggi,
                latitude: data.latitude,
                longitude: data.longitude,
                waktu: waktu
            });

            if (selectedDeviceId === deviceId || selectedDeviceId === '') {
                updateDashboard(data);
                refreshTable();
            }
        });

        function updateDashboard(data) {
            document.getElementById('berat').textContent = `${data.berat.toFixed(2)} gram`;
            document.getElementById('tinggi').textContent = `${data.tinggi} cm`;
            document.getElementById('location').innerHTML = `Lat: ${data.latitude}<br>Lng: ${data.longitude}`;
            document.getElementById('berat-progress').style.width = `${Math.min((data.berat / 100) * 100, 100)}%`;
            document.getElementById('tinggi-progress').style.width = `${Math.min((data.tinggi / 100) * 100, 100)}%`;
        }

        function refreshTable() {
            let tbody = document.getElementById('data-body');
            tbody.innerHTML = '';
            for (let deviceId in devices) {
                if (selectedDeviceId !== '' && deviceId !== selectedDeviceId) continue;
                devices[deviceId].data.forEach(item => {
                    tbody.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${deviceId}</td>
                            <td>${item.berat.toFixed(2)}</td>
                            <td>${item.tinggi}</td>
                            <td>${item.latitude}</td>
                            <td>${item.longitude}</td>
                            <td>${item.waktu}</td>
                        </tr>
                    `);
                });
            }
        }

        document.getElementById('device-select').addEventListener('change', function (e) {
            selectedDeviceId = e.target.value;
            if (selectedDeviceId && devices[selectedDeviceId]) {
                let latest = devices[selectedDeviceId].data[0];
                map.setView([latest.latitude, latest.longitude], 17);
                updateDashboard(latest);
            } else {
                map.setView([-6.914744, 107.609810], 13);
            }
            refreshTable();
        });

        function getRandomColor() {
            return `#${Math.floor(Math.random()*16777215).toString(16)}`;
        }
    </script>

    <style>
        @media (max-width: 576px) {
            h1 {
                font-size: 1.5rem;
                text-align: center;
            }
            #location {
                font-size: 0.9rem;
            }
        }
    </style>
</x-app-layout>
