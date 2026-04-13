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
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function trayekAsal()
    {
        return $this->hasMany(Trayek::class, 'id_kota_asal', 'id_kota');
    }

    public function trayekTujuan()
    {
        return $this->hasMany(Trayek::class, 'id_kota_tujuan', 'id_kota');
    }
}