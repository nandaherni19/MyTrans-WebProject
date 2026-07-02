<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;

class KendaraanUserController extends Controller
{
    // Guest
    public function guestIndex()
    {
        $kendaraans = Kendaraan::all();

        return view(
            'dashboard.user.katalogkendaraan',
            compact('kendaraans')
        );
    }

    public function guestDetail($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        return view(
            'dashboard.user.detailkendaraan',
            compact('kendaraan')
        );
    }

    // User login
    public function index()
    {
        $kendaraans = Kendaraan::all();

        return view(
            'dashboard.user.katalogkendaraan',
            compact('kendaraans')
        );
    }

    public function detail($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        return view(
            'dashboard.user.detailkendaraan',
            compact('kendaraan')
        );
    }

    public function booking($id)
{
    $kendaraan = Kendaraan::findOrFail($id);

    $tanggalMulai   = request('tanggal_mulai');
    $tanggalSelesai = request('tanggal_selesai');
    $jumlahPeserta  = request('jumlah_peserta');

    return view('dashboard.user.bookingkendaraan', compact(
        'kendaraan',
        'tanggalMulai',
        'tanggalSelesai',
        'jumlahPeserta'
    ));
}
}