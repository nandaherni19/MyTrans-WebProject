<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketGambar extends Model
{
    protected $table = 'ms_paket_gambar';
    protected $primaryKey = 'id_gambar';

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket');
    }
}
