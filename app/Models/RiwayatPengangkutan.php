<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengangkutan extends Model
{
    protected $fillable = [
        'device_id', 'berat', 'tinggi', 'latitude', 'longitude', 'waktu_pengangkutan', 'user_id'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
