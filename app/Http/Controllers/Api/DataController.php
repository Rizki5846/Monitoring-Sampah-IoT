<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceData;
use App\Models\JadwalPengangkutan;
use App\Models\User;
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

        // Jika penuh dan belum dikirim notifikasi
        if ($isPenuh && !$device->sudah_dikirim_wa) {
            $tambahan = $jadwal ? "\n\n🚛 *Hari ini jadwal pengangkutan. Silakan diangkut!*" : "";

            $pesan = "📦 *Tempat Sampah Penuh!*\n\n" .
                "🆔 Device: {$device->device_id}\n" .
                "📊 Berat: {$berat} gram\n" .
                "📏 Tinggi: {$tinggi} cm\n" .
                "📍 Lokasi: https://www.google.com/maps?q={$data['latitude']},{$data['longitude']}" .
                $tambahan;

            // Ambil semua petugas (user dengan nomor WhatsApp)
            $petugasList = User::whereNotNull('phone')->get();
            $terkirim = false;

            foreach ($petugasList as $petugas) {
                $response = Http::withHeaders([
                    'Authorization' => '3koPhLPnzgdbBC3NAvmd'
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $petugas->phone,
                    'message' => $pesan
                ]);

                if ($response->successful()) {
                    $terkirim = true;
                    Log::info("📤 WA dikirim ke petugas {$petugas->name} ({$petugas->phone}) untuk device {$device->device_id}");
                } else {
                    Log::error("❌ Gagal kirim WA ke {$petugas->phone}: " . $response->body());
                }
            }

            if ($terkirim) {
                $device->sudah_dikirim_wa = true;
                $device->save();
            }

        } 
        // Reset status jika tidak penuh
        elseif (!$isPenuh && $device->sudah_dikirim_wa) {
            $device->sudah_dikirim_wa = false;
            $device->save();
            Log::info("🔁 Reset status notifikasi untuk {$device->device_id}");
        }
    }

}
