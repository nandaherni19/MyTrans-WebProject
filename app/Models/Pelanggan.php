<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'ms_pelanggan';
    protected $primaryKey = 'id_pelanggan';

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_pelanggan');
    }
}
