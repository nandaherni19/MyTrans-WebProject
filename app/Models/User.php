<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'ms_users';     
    protected $primaryKey = 'id_users'; 
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'role',
        'otp',
        'otp_experes_at',
        'is_verified'
    ];

    protected $hidden = [
        'password',
    ];

    public $incrementing = true;
    protected $keyType = 'int'; 
}