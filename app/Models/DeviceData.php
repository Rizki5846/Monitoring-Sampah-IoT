<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    protected $fillable = ['device_id', 'berat', 'tinggi', 'latitude', 'longitude', 'diangkut_at'];


    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}
