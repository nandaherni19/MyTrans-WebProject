<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'ms_booking';
    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'tanggal_perjalanan',
        'jumlah_peserta',
        'total_biaya',
        'status_booking',
        'id_paket',
        'id_pelanggan'
    ];

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_booking');
    }
}
