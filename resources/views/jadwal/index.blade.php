<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-3">Kelola Jadwal Pengangkutan</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Form tambah jadwal -->
        <form action="{{ route('jadwal.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <select name="hari" class="form-select">
                    <option disabled selected>Pilih hari</option>
                    @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Monday'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <!-- Tabel jadwal -->
        <table class="table table-bordered mb-5">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $j)
                <tr>
                    <td>{{ $j->hari }}</td>
                    <td>
                        <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Kartu semua hari -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-5">
            @php
                $semuaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu','Wednesday','Monday'];
            @endphp

            @foreach ($semuaHari as $hari)
                @php
                    $adaJadwal = $jadwal->contains('hari', $hari);
                    $isToday = $hari === $hariIni;
                @endphp

                <div class="bg-white rounded-lg shadow p-4 border {{ $isToday ? 'border-blue-500' : 'border-gray-200' }}">
                    <h2 class="text-lg font-semibold {{ $isToday ? 'text-blue-600' : 'text-gray-800' }}">{{ $hari }}</h2>

                    @if ($adaJadwal)
                        <div class="mt-2 text-green-600 font-medium">
                            ✅ Ada Pengangkutan
                        </div>
                    @else
                        <div class="mt-2 text-gray-500">
                            ❌ Tidak Ada Jadwal
                        </div>
                    @endif

                    @if ($isToday)
                        <div class="mt-3 text-blue-500 font-semibold">📍 Hari Ini</div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- ♻️ Tempat Sampah Penuh -->
        <div id="tempat-sampah-section">
           <!-- ♻️ Tempat Sampah Penuh -->
            @if ($deviceSiapAngkut && count($deviceSiapAngkut))
                <div class="mb-4">
                    <h4 class="text-xl font-semibold text-green-600 mb-3">
                        🚛 Tempat Sampah Penuh yang Akan Diangkut Hari Ini ({{ $hariIni }})
                    </h4>

                    <div class="row">
                        @foreach ($deviceSiapAngkut as $device)
                            <div class="col-md-4 mb-3">
                                <div class="card border-success shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">🆔 {{ $device->device_id }}</h5>
                                        <p class="mb-1">📊 Berat: {{ $device->latestData->berat }} gram</p>
                                        <p class="mb-1">📏 Tinggi: {{ $device->latestData->tinggi }} cm</p>
                                        <a href="https://www.google.com/maps?q={{ $device->latestData->latitude }},{{ $device->latestData->longitude }}" class="text-primary d-block mb-2" target="_blank">
                                            📍 Lihat Lokasi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-muted mt-3">
                    Tidak ada tempat sampah penuh yang perlu diangkut hari ini.
                </div>
            @endif

        </div>
    </div>

    <!-- 🔄 Auto-refresh bagian tempat sampah -->
    <script>
        setInterval(() => {
            fetch("{{ route('jadwal.index') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const updatedSection = doc.querySelector('#tempat-sampah-section');
                document.getElementById('tempat-sampah-section').innerHTML = updatedSection.innerHTML;
            });
        }, 5000); // 30 detik
    </script>
</x-app-layout>
