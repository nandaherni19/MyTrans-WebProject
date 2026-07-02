<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingKendaraan extends Model
{
    protected $table = 'ms_booking_kendaraan';

    protected $primaryKey = 'id_booking_kendaraan';

    protected $fillable = [
        'id_kendaraan',
        'nama',
        'whatsapp',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_peserta',
        'tujuan',
        'pickup',
        'catatan',
        'status_booking'
    ];
}