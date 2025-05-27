<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['device_id', 'nama'];

    public function data()
    {
        return $this->hasMany(DeviceData::class, 'device_id', 'device_id');
    }

    public function latestData()
    {
        return $this->hasOne(DeviceData::class, 'device_id', 'device_id')->latestOfMany();
    }


}
