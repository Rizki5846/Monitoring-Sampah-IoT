<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4 text-2xl font-bold">📜 Riwayat Pengangkutan</h2>

        @if($riwayat->isEmpty())
            <div class="alert alert-info">
                Belum ada riwayat pengangkutan.
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($riwayat as $r)
                <div class="bg-white rounded-lg shadow border p-4">
                    <div class="text-sm text-gray-500 mb-1">
                        🕒 {{ \Carbon\Carbon::parse($r->waktu_pengangkutan)->format('d M Y - H:i') }}
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mb-1">
                        🆔 {{ $r->device->device_id }}
                    </h3>
                    <div class="text-sm">
                        <div>📊 Berat: <span class="font-semibold">{{ $r->berat }} gram</span></div>
                        <div>📏 Tinggi: <span class="font-semibold">{{ $r->tinggi }} cm</span></div>
                        <div class="mt-2">
                            📍 <a href="https://maps.google.com?q={{ $r->latitude }},{{ $r->longitude }}" target="_blank" class="text-blue-500 underline">Lihat Lokasi</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
