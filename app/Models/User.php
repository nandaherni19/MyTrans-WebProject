<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'ms_users';     
    protected $primaryKey = 'id_users'; 
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'no_ktp',
        'alamat',
        'role',
        'otp',
        'otp_expires_at',
        'is_verified'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'otp_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Relations
     */
    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_users', 'id_users');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_users', 'id_users');
    }
}