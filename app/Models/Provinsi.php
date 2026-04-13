<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $table = 'ms_provinsi';
    protected $primaryKey = 'id_provinsi';
    public $timestamps = false;

    protected $fillable = [
        'nama_provinsi',
    ];

    public function kota()
    {
        return $this->hasMany(Kota::class, 'id_provinsi', 'id_provinsi');
    }
}