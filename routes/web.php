<?php

use App\Http\Controllers\AngkutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\JadwalController;
use App\Models\RiwayatPengangkutan;

// 🌐 Halaman publik (bisa diakses warga tanpa login)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

Route::get('/pengangkutan/riwayat', function () {
    $riwayat = RiwayatPengangkutan::with('device')->latest()->get();
    return view('pengangkutan.index', compact('riwayat'));
})->name('pengangkutan.index');

// 🛡️ Halaman admin (harus login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD device → hanya admin
    Route::resource('devices', DeviceController::class);

    // Tambah dan hapus jadwal → hanya admin
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Tombol angkut
    Route::post('/jadwal/angkut/{device}', [JadwalController::class, 'angkut'])->name('jadwal.angkut');
});

// 🔐 Auth scaffolding
require __DIR__.'/auth.php';

