<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kota;
use App\Models\TitikJemput;

class Booking extends Model
{
    protected $table = 'ms_booking';
    protected $primaryKey = 'id_booking';
    public $timestamps = false;

    protected $fillable = [
        'jumlah_peserta',
        'total_biaya',
        'status_booking',
        'tipe_booking',
        'tipe_pembayaran',
        'opsi_pembayaran',
        'id_paket',
        'id_users',
        'id_kota_layanan',   // tambah ini
        'id_kota_asal',       // tambah ini
        'id_titik_jemput',    // tambah ini
        'tanggal_berangkat',
        'tanggal_kembali',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_kembali'   => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'jumlah_peserta'    => 'integer',
        'total_biaya'       => 'integer',
    ];

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'id_paket', 'id_paket');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }

    public function penumpangs()
    {
        return $this->hasMany(Penumpang::class, 'id_booking', 'id_booking');
    }

    public function kendaraans()
    {
        return $this->belongsToMany(
            Kendaraan::class,
            'tr_booking_kendaraan',
            'id_booking',
            'id_kendaraan'
        );
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'id_booking', 'id_booking');
    }

    public function pembayaranTerakhir()
    {
        return $this->hasOne(Pembayaran::class, 'id_booking', 'id_booking')
            ->latest('id_pembayaran');
    }

    public function kotaAsal()
    {
        return $this->belongsTo(Kota::class, 'id_kota_asal', 'id_kota');
    }

    public function kotaLayanan()
    {
        return $this->belongsTo(Kota::class, 'id_kota_layanan', 'id_kota');
    }

    public function titikJemput()
    {
        return $this->belongsTo(TitikJemput::class, 'id_titik_jemput', 'id_titik_jemput');
    }
}