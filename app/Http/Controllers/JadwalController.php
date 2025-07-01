<?php

namespace App\Http\Controllers;

use App\Models\JadwalPengangkutan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = JadwalPengangkutan::all();
        return view('jadwal.index', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu,Tuesday',
        ]);

        JadwalPengangkutan::firstOrCreate(['hari' => $request->hari]);

        return redirect()->back()->with('success', 'Jadwal ditambahkan!');
    }

    public function destroy($id)
    {
        JadwalPengangkutan::destroy($id);
        return redirect()->back()->with('success', 'Jadwal dihapus!');
    }
}
