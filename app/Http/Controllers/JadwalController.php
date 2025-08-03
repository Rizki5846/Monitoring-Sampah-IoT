<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\JadwalPengangkutan;
use App\Models\RiwayatPengangkutan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
   public function index()
    {
        $jadwal = JadwalPengangkutan::all();
        $hariIni = Carbon::now()->translatedFormat('l');

        $jadwalAktif = JadwalPengangkutan::where('hari', $hariIni)->exists();

        $deviceSiapAngkut = collect();
        if ($jadwalAktif) {
            $sudahDiangkutHariIni = RiwayatPengangkutan::whereDate('waktu_pengangkutan', Carbon::today())
                ->pluck('device_id');

            $deviceSiapAngkut = Device::with('latestData')
                ->get()
                ->filter(function ($device) use ($sudahDiangkutHariIni) {
                    $data = $device->latestData;
                    return $data && ($data->berat >= 900 || $data->tinggi >= 90)
                        && !$sudahDiangkutHariIni->contains($device->id);
        });
        }

        return view('jadwal.index', compact('jadwal', 'hariIni', 'deviceSiapAngkut'));
    }

     public function angkut(Device $device)
        {
            $latest = $device->latestData;

            if (!$latest) {
                return redirect()->back()->with('error', 'Data sensor tidak ditemukan.');
            }

            RiwayatPengangkutan::create([
                'device_id' => $device->id,
                'berat' => $latest->berat,
                'tinggi' => $latest->tinggi,
                'latitude' => $latest->latitude,
                'longitude' => $latest->longitude,
                'waktu_pengangkutan' => now(),
                'user_id'=> Auth::id(),
            ]);

            // Kirim WA ke semua petugas
            $user = Auth::user();
            $pesan = "✅ *Tempat Sampah Sudah Diangkut!*\n\n" .
                "🆔 Device: {$device->device_id}\n" .
                "📊 Berat: {$latest->berat} gram\n" .
                "📏 Tinggi: {$latest->tinggi} cm\n" .
                "📍 Lokasi: https://www.google.com/maps?q={$latest->latitude},{$latest->longitude}\n\n" .
                "👷 Petugas: {$user->name}";

            $this->kirimWhatsapp($pesan); // Tanpa parameter nomor

            return redirect()->back()->with('success', 'Tempat sampah berhasil ditandai sudah diangkut.');
        }

    private function kirimWhatsapp($pesan)
    {
        $petugasList = User::whereNotNull('phone')->get();

        foreach ($petugasList as $petugas) {
            $response = Http::withHeaders([
                'Authorization' => '3koPhLPnzgdbBC3NAvmd' // Ganti dengan token asli
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $petugas->phone,
                'message' => $pesan,
            ]);

            if ($response->successful()) {
                Log::info("📤 Notifikasi terkirim ke {$petugas->name} ({$petugas->phone})");
            } else {
                Log::error("❌ Gagal kirim ke {$petugas->phone}: " . $response->body());
            }
        }
     }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
        ]);

        JadwalPengangkutan::firstOrCreate(['hari' => $request->hari]);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        JadwalPengangkutan::destroy($id);
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}


