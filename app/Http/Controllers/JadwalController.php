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
            $deviceSiapAngkut = Device::with('latestData')
                ->get()
                ->filter(function ($device) {
                    $data = $device->latestData;
                    return $data && ($data->berat >= 900 || $data->tinggi >= 90);
                });
        }

        return view('jadwal.index', compact('jadwal', 'hariIni', 'deviceSiapAngkut'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu,Wednesday,Monday',
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



    // public function tandaiDiangkut(Request $request, $deviceId)
    // {
    //     $device = Device::where('device_id', $deviceId)->firstOrFail();
    //     $latest = $device->latestData;

    //     if ($latest) {
    //         RiwayatPengangkutan::create([
    //             'device_id' => $device->device_id,
    //             'berat' => $latest->berat,
    //             'tinggi' => $latest->tinggi,
    //             'latitude' => $latest->latitude,
    //             'longitude' => $latest->longitude,
    //             'waktu_angkut' => Carbon::now(),
    //         ]);
    //     }

    //     // Update status
    //     $device->diangkut_at = now();
    //     $device->save();

    //     return redirect()->back()->with('success', '✅ Data pengangkutan disimpan.');
    // }