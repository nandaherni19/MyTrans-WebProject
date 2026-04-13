<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanTransaksiController extends Controller
{
    public function index()
    {
        return view('dashboard.superadmin.kelola-laporan-transaksi');
}
}



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class LaporantransaksiController extends Controller
// {
//     public function index()
//     {
//         $transaksis = [
//             [
//                 'id' => 'BK001',
//                 'nama' => 'Asya Farasya',
//                 'paket' => 'Pantai Watu Karung',
//                 'tanggal' => '17/03/2026',
//                 'total' => 'Rp 1.500.000',
//                 'dibayar' => 'Rp 1.500.000',
//                 'status' => 'Lunas',
//                 'metode' => 'QRIS'
//             ]
//         ];

//         return view('admin.kelola-laporantransaksi', compact('transaksis'));
//     }
// }