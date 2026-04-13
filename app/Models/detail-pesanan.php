<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;

class DetailPesananController extends Controller
{
    public function show($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        return view('user.detailpesanan', compact('pemesanan'));
    }
}
