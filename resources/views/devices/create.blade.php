<x-app-layout>
    <div class="container py-4">
        <h1 class="fw-bold mb-4">Tambah Device</h1>

        <form action="{{ route('devices.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="device_id" class="form-label">Device ID</label>
                <div class="input-group">
                    <input type="text" class="form-control @error('device_id') is-invalid @enderror" id="device_id" name="device_id" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="startScanner()">Scan QR</button>
                </div>
                @error('device_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tempat untuk menampilkan scanner -->
            <div id="scanner" class="mt-3 rounded" style="width: 300px; display: none;"></div>

            <button type="submit" class="btn btn-primary mt-3">Tambah Device</button>
        </form>
    </div>

    {{-- Script QR Code Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrCode;

        function startScanner() {
            const scannerDiv = document.getElementById("scanner");
            scannerDiv.style.display = "block";

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("scanner");
            }

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    html5QrCode.start(
                        { facingMode: "environment" }, // kamera belakang jika tersedia
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        qrCodeMessage => {
                            document.getElementById("device_id").value = qrCodeMessage;
                            html5QrCode.stop();
                            scannerDiv.style.display = "none";
                        },
                        errorMessage => {
                            // error sementara saat scanning — bisa diabaikan
                        }
                    );
                }
            }).catch(err => {
                alert("Tidak bisa mengakses kamera: " + err);
            });
        }
    </script>
</x-app-layout>
