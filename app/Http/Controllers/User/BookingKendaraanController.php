<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\BookingKendaraan;

class BookingKendaraanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_kendaraan'    => 'required',
            'nama'            => 'required|string|max:100',
            'whatsapp'        => 'required|string|max:20',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_peserta'  => 'required|integer|min:1',
            'tujuan'          => 'required|string|max:255',
        ]);

        $kendaraan = Kendaraan::findOrFail($request->id_kendaraan);

        $booking = BookingKendaraan::create([
            'id_kendaraan'    => $request->id_kendaraan,
            'nama'            => $request->nama,
            'whatsapp'        => $request->whatsapp,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_peserta'  => $request->jumlah_peserta,
            'tujuan'          => $request->tujuan,
            'pickup'          => $request->pickup,
            'catatan'         => $request->catatan,
            'status'          => 'Menunggu Konfirmasi',
        ]);

        $pesan = "Halo Admin MyTrans\n\n";
        $pesan .= "Saya ingin melakukan booking kendaraan.\n\n";
        $pesan .= "Nama : {$booking->nama}\n";
        $pesan .= "Kendaraan : {$kendaraan->nama_kendaraan}\n";
        $pesan .= "Tanggal Berangkat : {$booking->tanggal_mulai}\n";
        $pesan .= "Tanggal Pulang : {$booking->tanggal_selesai}\n";
        $pesan .= "Jumlah Peserta : {$booking->jumlah_peserta}\n";
        $pesan .= "Tujuan : {$booking->tujuan}\n";
        $pesan .= "Lokasi Jemput : {$booking->pickup}\n";
        $pesan .= "Catatan : {$booking->catatan}\n\n";
        $pesan .= "Mohon informasi ketersediaan kendaraan dan pembayaran.";

        $nomorAdmin = "6281234567890"; // ganti nomor WA admin

        return redirect(
            "https://wa.me/{$nomorAdmin}?text=" . urlencode($pesan)
        );
    }
}   