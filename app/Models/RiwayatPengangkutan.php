<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengangkutan extends Model
{
    protected $fillable = [
        'device_id', 'berat', 'tinggi', 'latitude', 'longitude', 'waktu_pengangkutan'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
