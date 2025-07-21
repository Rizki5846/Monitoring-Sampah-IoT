<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    // Menampilkan daftar device
    public function index()
    {
        $devices = Device::with(['latestData'])->get(); // gunakan relasi latestData
        return view('devices.index', compact('devices'));
    }

    // Menampilkan form tambah device
    public function create()
    {
        return view('devices.create');
    }

    // Menyimpan device yang baru ditambahkan
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|unique:devices,device_id',
            'nama_device' => 'required|string|max:255', // tambahkan validasi nama
        ]);

        Device::create([
            'device_id' => $request->device_id,
            'nama_device' => $request->nama_device, // simpan nama device
        ]);

        return redirect()->route('devices.index')->with('success', 'Device berhasil ditambahkan.');
    }



}
