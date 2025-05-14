<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil semua device yang terdaftar
        $devices = Device::all();
        return view('dashboard', compact('devices'));
    }
}
