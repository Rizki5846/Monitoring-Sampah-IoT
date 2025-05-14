<x-app-layout>
    <div class="container py-4">
        <h1 class="fw-bold mb-4">Daftar Device</h1>
        
        <!-- Pesan sukses setelah menambah device -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Tambah Device</a>

        <!-- Tabel Daftar Device -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Device ID</th>
                    <th>Waktu Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $device)
                    <tr>
                        <td>{{ $device->device_id }}</td>
                        <td>{{ $device->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
