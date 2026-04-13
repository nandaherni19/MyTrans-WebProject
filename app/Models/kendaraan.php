<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'ms_kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $incrementing = true;
    protected $keyType = 'int';  

    protected $fillable = [
        'nama_kendaraan', 
        'jenis_kendaraan', 
        'kapasitas',
        'plat_nomor',
        'harga_sewa',
        'status_kendaraan', 
        'foto_kendaraan'
    ];
}