<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaketWisata;
use App\Models\Booking;

class Kendaraan extends Model
{
    protected $table = 'ms_kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $timestamps = false;

    protected $fillable = [
        'nama_kendaraan',
        'jenis_kendaraan',
        'kapasitas',
        // 'plat_nomor',
        'harga_sewa',
        'status_kendaraan',
        'foto_kendaraan',
        // 'fasilitas'
    ];

    public function paketWisata()
    {
        return $this->hasMany(PaketWisata::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'tr_booking_kendaraan',
            'id_kendaraan',
            'id_booking'
        );
    }
}