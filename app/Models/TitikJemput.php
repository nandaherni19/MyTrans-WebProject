<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikJemput extends Model
{
    protected $table = 'ms_titik_jemput';
    protected $primaryKey = 'id_titik_jemput';
    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];

    public function pakets()
    {
        return $this->belongsToMany(
            PaketWisata::class,
            'tr_titik_jemput',
            'id_titik_jemput',
            'id_paket'
        );
    }

        public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_titik_jemput', 'id_titik_jemput');
    }
}