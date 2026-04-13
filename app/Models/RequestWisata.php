<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestWisata extends Model
{
    protected $table = 'ms_request_wisata';
    protected $primaryKey = 'id_request';

    protected $fillable = [
        'id_users',
        'id_kota_asal',
        'id_kota_tujuan',
        'tanggal_keberangkatan',
        'jumlah_peserta',
        'durasi',
        'catatan',
        'estimasi_harga',
        'status_request',
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    // RELASI KE KOTA ASAL
    public function kotaAsal()
    {
        return $this->belongsTo(Kota::class, 'id_kota_asal', 'id_kota');
    }

    // RELASI KE KOTA TUJUAN
    public function kotaTujuan()
    {
        return $this->belongsTo(Kota::class, 'id_kota_tujuan', 'id_kota');
    }
}