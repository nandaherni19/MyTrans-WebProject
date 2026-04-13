<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trayek extends Model
{
    protected $table = 'ms_trayek_wisata';
    protected $primaryKey = 'id_trayek';

    public $timestamps = false; // kalau tabelmu gak ada created_at

    protected $fillable = [
        'kode_trayek',
        'id_kota_asal',
        'id_kota_tujuan',
    ];

    // RELASI KE PAKET WISATA
    public function kotaAsal()
    {
        return $this->belongsTo(Kota::class, 'id_kota_asal', 'id_kota');
    }

    public function kotaTujuan()
    {
        return $this->belongsTo(Kota::class, 'id_kota_tujuan', 'id_kota');
    }

    public function paketWisata()
    {
        return $this->hasMany(PaketWisata::class, 'id_trayek', 'id_trayek');
    }
}