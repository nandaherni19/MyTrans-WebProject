<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

        return view('welcome', compact('paketTerbaru'));
    }
}