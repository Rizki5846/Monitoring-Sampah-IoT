<?php

use App\Http\Controllers\Api\DataApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;



// routes/api.php
use App\Http\Controllers\Api\DataController;

Route::post('/data', [DataController::class, 'store']);
Route::get('/dashboard-data', [DataApiController::class, 'index']);





require __DIR__.'/auth.php';