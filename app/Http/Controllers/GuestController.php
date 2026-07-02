<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function welcome()
    {
        // hanya admin/superadmin yang diarahkan
        if (Auth::check()) {
            if (
                Auth::user()->role == 'admin' ||
                Auth::user()->role == 'superadmin'
            ) {
                return redirect()->route('dashboard.beranda-admin');
            }
        }

        $paketTerbaru = PaketWisata::with(['kota.provinsi', 'kendaraan'])
            ->where('status', 'aktif')
            ->latest('id_paket')
            ->get()
            ->filter(function ($paket) {
                if ($paket->tipe === 'open_trip') {
                    return $paket->sisa_kursi > 0;
                }
                return true;
            })
            ->take(3);

        $kendaraanTerbaru = Kendaraan::where('status_kendaraan', 'tersedia')
            ->latest('id_kendaraan')
            ->take(3)
            ->get();

        return view('welcome', compact(
            'paketTerbaru',
            'kendaraanTerbaru'
        ));
    }
}