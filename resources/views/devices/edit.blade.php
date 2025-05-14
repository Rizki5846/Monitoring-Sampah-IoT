<x-app-layout>
    <div class="container py-4">
        <h2>Edit Device</h2>

        <form action="{{ route('devices.update', $device) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Device ID</label>
                <input type="text" name="device_id" class="form-control" value="{{ $device->device_id }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Lokasi</label>
                <input type="text" name="name" class="form-control" value="{{ $device->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Latitude</label>
                <input type="text" name="latitude" class="form-control" value="{{ $device->latitude }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Longitude</label>
                <input type="text" name="longitude" class="form-control" value="{{ $device->longitude }}">
            </div>
            <button class="btn btn-success">Update</button>
        </form>
    </div>
</x-app-layout>
