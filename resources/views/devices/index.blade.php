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
            <th>Nama Device</th>
            <th>Waktu Dibuat</th>
            <th>Status</th> {{-- Kolom baru --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($devices as $device)
            @php
                $data = $device->latestData;
                $status = 'Tidak Ada Data';

                if ($data) {
                    $beratFull = $device->berat_threshold ?? 5000;
                    $tinggiFull = $device->tinggi_threshold ?? 80;

                    if ($data->berat >= $beratFull || $data->tinggi >= $tinggiFull) {
                        $status = 'Penuh';
                    } else {
                        $status = 'Normal';
                    }
                }
            @endphp
            <tr>
                <td>{{ $device->device_id }}</td>
                <td>{{ $device->nama_device }}</td>
                <td>{{ $device->created_at }}</td>
                <td>
                    <span class="badge bg-{{ $status == 'Penuh' ? 'danger' : ($status == 'Normal' ? 'success' : 'secondary') }}">
                        {{ $status }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
 </div>
</x-app-layout>
