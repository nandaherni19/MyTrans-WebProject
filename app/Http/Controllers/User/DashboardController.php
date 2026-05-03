<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;

class DashboardController extends Controller
{
    public function index()
{
    $pakets = PaketWisata::with(['kota.provinsi', 'kendaraan'])
        ->where('status', 'aktif')
        ->get()
        ->filter(function ($paket) {
            if ($paket->tipe === 'open_trip') {
                return $paket->sisa_kursi > 0;
            }

            return true;
        })
        ->take(3);

    return view('dashboard.beranda-user', compact('pakets'));
}
}