<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone', // Tambahkan phone ke fillable
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Accessor untuk format nomor telepon
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) return null;
        
        return '+62' . substr($this->phone, 2);
    }

    // Scope untuk pencarian petugas berdasarkan nomor
    public function scopeByPhone($query, $phone)
    {
        $formattedPhone = $this->formatPhone($phone);
        return $query->where('phone', $formattedPhone);
    }

    // Helper untuk format nomor
    public static function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        
        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }
        
        return $phone;
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function devices()
    {
        return $this->hasMany(Device::class, 'petugas_id');
    }
}
