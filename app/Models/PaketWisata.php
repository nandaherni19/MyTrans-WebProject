<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaketWisata extends Model
{
    protected $table = 'ms_paket_wisata';
    protected $primaryKey = 'id_paket';

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
        'durasi',
        'gambar',
        'id_trayek',
        'id_kendaraan',
        'tanggal_keberangkatan',
        'status',
        'kapasitas',
        'fasilitas_didapat',
    ];

    public function trayek()
    {
        return $this->belongsTo(Trayek::class, 'id_trayek', 'id_trayek');
    }

    public function kendaraan(){
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
        ->whereHas('pembayaran', function ($query) {
            $query->whereIn('status_booking', ['dp', 'lunas']);
        })
        ->sum('jumlah_peserta');
}

public function getSisaKursiAttribute()
{
    return max(0, $this->kapasitas - $this->terisi);
}

    public function getStatusAutoAttribute()
    {
        $tanggal = Carbon::parse($this->tanggal_keberangkatan)->endOfDay();

        if ($tanggal < now()) {
            return 'lewat';
        }

        // if ($this->kapasitas <= 0) {
        //     return 'penuh';
        // }

         if ($this->sisa_kursi <= 0) {
        return 'penuh';
        }



        // buat booking
        // $totalBooking = $this->bookings()->sum('jumlah_orang');

        // if ($totalBooking >= $this->kapasitas) {
        //     return 'penuh';
        // }

        return 'aktif';
    }
}