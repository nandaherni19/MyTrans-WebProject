<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $table = 'ms_kota';
    protected $primaryKey = 'id_kota';
    public $timestamps = false;

    protected $fillable = [
        'nama_kota',
        'id_provinsi',
        'is_area_cabang',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function paketWisata()
    {
        return $this->hasMany(PaketWisata::class, 'id_kota', 'id_kota');
    }

    public function paketLayanan()
    {
        return $this->belongsToMany(
            PaketWisata::class,
            'tr_paket_kota',
            'id_kota',
            'id_paket'
        );
    }
}