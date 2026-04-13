<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use Illuminate\Http\Request;

class PaketWisataUserController extends Controller
{
    public function index()
    {
        $pakets = PaketWisata::with(['trayek.kotaAsal', 'trayek.kotaTujuan'])->get();
        return view('dashboard.user.katalogpaketwisata', compact('pakets'));
    }

    public function guestIndex()
    {
        $pakets = PaketWisata::with(['trayek.kotaAsal', 'trayek.kotaTujuan'])->get();
        return view('guest.katalogpaketwisata', compact('pakets'));
    }

    public function detail($id)
    {
        $paket = PaketWisata::with(['trayek.kotaAsal', 'trayek.kotaTujuan'])->findOrFail($id);
        return view('dashboard.user.detailpaket', compact('paket'));
    }

    public function guestDetail($id)
    {
        $paket = PaketWisata::with(['trayek.kotaAsal', 'trayek.kotaTujuan'])->findOrFail($id);
        return view('guest.detailpaket', compact('paket'));
    }
}