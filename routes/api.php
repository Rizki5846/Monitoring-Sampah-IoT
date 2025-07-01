<?php

use App\Http\Controllers\Api\DataApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Models\JadwalPengangkutan;


// routes/api.php
use App\Http\Controllers\Api\DataController;

Route::post('/data', [DataController::class, 'store']);
Route::get('/dashboard-data', [DataApiController::class, 'index']);




Route::get('/jadwal-hari-ini', function () {
    $hari = now()->locale('id')->translatedFormat('l'); // e.g. "Senin", "Kamis"
    $ada = JadwalPengangkutan::where('hari', $hari)->exists();
    return response()->json(['scheduled' => $ada]);
});




require __DIR__.'/auth.php';