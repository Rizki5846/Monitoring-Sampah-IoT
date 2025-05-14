<x-app-layout>
    <div class="container py-4">
        <h1 class="fw-bold mb-4">Tambah Device</h1>

        <form action="{{ route('devices.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="device_id" class="form-label">Device ID</label>
                <input type="text" class="form-control @error('device_id') is-invalid @enderror" id="device_id" name="device_id" required>
                @error('device_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Tambah Device</button>
        </form>
    </div>
</x-app-layout>
