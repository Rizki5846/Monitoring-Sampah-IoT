<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceData;
use App\Models\JadwalPengangkutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        // ✅ Validasi device
        $device = Device::where('device_id', $data['device_id'])->first();
        if (!$device) {
            return response()->json(['error' => 'Device tidak terdaftar'], 400);
        }

        // ✅ Simpan data ke tabel device_data
        $device->data()->create([
            'berat' => $data['berat'],
            'tinggi' => $data['tinggi'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        // ✅ Cek jadwal dan kirim notifikasi jika perlu
        $this->cekDanKirimNotifikasi($device, $data);

        return response()->json(['message' => 'Data berhasil disimpan']);
    }

    private function cekDanKirimNotifikasi($device, $data)
    {
        $hariIni = Carbon::now()->translatedFormat('l'); // Senin, Selasa, dll
        $berat = $data['berat'];
        $tinggi = $data['tinggi'];
        $jadwal = JadwalPengangkutan::where('hari', $hariIni)->exists();

        $isPenuh = $berat >= 900 || $tinggi >= 90;

        // ✅ Jika penuh dan belum dikirim
        if ($isPenuh && !$device->sudah_dikirim_wa) {
            // Tambahkan pesan tambahan jika hari ini adalah jadwal
            $tambahan = $jadwal ? "\n\n🚛 *Hari ini jadwal pengangkutan. Silakan diangkut!*" : "";

            $pesan = "📦 *Tempat Sampah Penuh!*\n\n" .
                "🆔 Device: {$device->device_id}\n" .
                "📊 Berat: {$berat} gram\n" .
                "📏 Tinggi: {$tinggi} cm\n" .
                "📍 Lokasi: https://www.google.com/maps?q={$data['latitude']},{$data['longitude']}" .
                $tambahan;

            $response = Http::withHeaders([
                'Authorization' => '3koPhLPnzgdbBC3NAvmd'
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => '6285158332699',
                'message' => $pesan
            ]);

            if ($response->successful()) {
                $device->sudah_dikirim_wa = true;
                $device->save();
                Log::info("📤 WA dikirim ke {$device->device_id}");
            } else {
                Log::error("❌ Gagal kirim WA: " . $response->body());
            }
        }

        // ✅ Reset jika tidak penuh
        if (!$isPenuh && $device->sudah_dikirim_wa) {
            $device->sudah_dikirim_wa = false;
            $device->save();
            Log::info("🔁 Reset status notifikasi untuk {$device->device_id}");
        }
    }

}
