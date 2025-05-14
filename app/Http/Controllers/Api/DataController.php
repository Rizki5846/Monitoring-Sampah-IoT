<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function store(Request $request)
    {
        $deviceId = $request->input('device_id');

        // ❗ Cek apakah device sudah terdaftar
        if (!Device::where('device_id', $deviceId)->exists()) {
            return response()->json(['error' => 'Device not registered'], 403);
        }

        DeviceData::create([
            'device_id' => $deviceId,
            'berat' => $request->input('berat'),
            'tinggi' => $request->input('tinggi'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        return response()->json(['message' => 'Data stored']);
    }
}
