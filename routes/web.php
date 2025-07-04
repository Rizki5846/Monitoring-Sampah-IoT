<?php

use App\Http\Controllers\AngkutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\JadwalController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('devices', DeviceController::class);
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::post('/jadwal/angkut/{device}', [\App\Http\Controllers\JadwalController::class, 'angkut'])->name('jadwal.angkut');
    Route::get('/pengangkutan/riwayat', function () {
        $riwayat = \App\Models\RiwayatPengangkutan::with('device')->latest()->get();
        return view('pengangkutan.index', compact('riwayat'));
        })->name('pengangkutan.index');




});

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');






require __DIR__.'/auth.php';
