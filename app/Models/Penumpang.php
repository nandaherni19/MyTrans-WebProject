<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penumpang extends Model
{
    protected $table = 'ms_penumpang';
    protected $primaryKey = 'id_penumpang';
    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'id_users',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }
}