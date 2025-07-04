<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DataApiController extends Controller
{
    public function index()
    {
        return DeviceData::latest()->take(20)->get();
    }
}
