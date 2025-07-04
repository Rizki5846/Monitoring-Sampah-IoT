<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\JadwalPengangkutan;
use App\Models\RiwayatPengangkutan;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        ]);

        return redirect()->back()->with('success', 'Tempat sampah berhasil ditandai sudah diangkut.');
    }


    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu,Wednesday,Monday,Friday',
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


