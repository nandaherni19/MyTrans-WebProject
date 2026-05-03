<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaketWisata extends Model
{
    protected $table = 'ms_paket_wisata';
    protected $primaryKey = 'id_paket';
    public $timestamps = false;

    protected $fillable = [
        'id_kota',
        'nama_paket',
        'tipe',
        'kapasitas',
        'min_peserta',
        'deskripsi',
        'harga',
        'durasi',
        'gambar',
        'fasilitas',
        'status',
        'id_kendaraan',
        'tanggal_berangkat',
        'tanggal_kembali',
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'id_kota', 'id_kota');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function galeri()
    {
        return $this->hasMany(PaketGambar::class, 'id_paket', 'id_paket');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_paket', 'id_paket');
    }

    public function getTerisiAttribute()
    {
        return $this->bookings()
            ->whereHas('pembayaranTerakhir', function ($query) {
                $query->whereIn('transaction_status', ['pending', 'berhasil']);
            })
            ->sum('jumlah_peserta');
    }

    public function getSisaKursiAttribute()
    {
        if ($this->tipe !== 'open_trip' || is_null($this->kapasitas)) {
            return null;
        }

        return max(0, $this->kapasitas - $this->terisi);
    }

    public function getStatusAutoAttribute()
    {
        if ($this->tipe === 'open_trip' && $this->tanggal_berangkat) {
            $tanggal = Carbon::parse($this->tanggal_berangkat)->endOfDay();

            if ($tanggal < now()) {
                return 'lewat';
            }

            if ($this->sisa_kursi <= 0) {
                return 'penuh';
            }
        }

        return $this->status;
    }

    public function titikJemput()
    {
        return $this->belongsToMany(
            TitikJemput::class,
            'tr_titik_jemput',
            'id_paket',
            'id_titik_jemput'
        );
    }

    public function kotaLayanan()
    {
        return $this->belongsToMany(
            Kota::class,
            'tr_paket_kota',
            'id_paket',
            'id_kota'
        );
    }
}