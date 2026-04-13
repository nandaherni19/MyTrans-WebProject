<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'tr_pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'jenis_pembayaran',
        'jumlah_bayar',
        'tanggal_bayar',
        'status_booking', // harus e  d ganti status pembayaran ga si, enum e pending, dp. lunas, gagal
        'id_booking'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }
}
