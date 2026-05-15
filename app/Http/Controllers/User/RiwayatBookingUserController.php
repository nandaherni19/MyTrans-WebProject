<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class RiwayatBookingUserController extends Controller
{
    public function index($filter = 'semua', $page = null)
    {
        $user = Auth::user();
        $riwayat = collect();

        $bookings = Booking::with([
            'paket.kota.provinsi',
            'pembayarans',
            'pembayaranTerakhir'
        ])
            ->where('id_users', $user->id_users)
             // booking 3 bulan terakhir
            ->where('created_at', '>=', now()->subMonths(3))
            ->get();

        foreach ($bookings as $item) {
            $lokasi = '-';

            if ($item->paket && $item->paket->kota) {
                $lokasi = $item->paket->kota->nama_kota;

                if ($item->paket->kota->provinsi) {
                    $lokasi .= ', ' . $item->paket->kota->provinsi->nama_provinsi;
                }
            }

            $judul = $item->paket->nama_paket ?? 'Booking Wisata';

            // hitung total pembayaran berhasil
            $sudahBayar = $item->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');

            $sisaBayar = $item->total_biaya - $sudahBayar;

            // tentukan label pembayaran
            if ($sisaBayar > 0) {
                $paymentLabel = 'DP';
            } else {
                $paymentLabel = 'Lunas';
            }

            $riwayat->push([
                'jenis' => 'booking',
                'id' => $item->id_booking,
                'judul' => $judul,
                'booking_id' => 'BK' . str_pad($item->id_booking, 3, '0', STR_PAD_LEFT),
                'lokasi' => $lokasi,
                'tanggal' => $item->created_at,
                'jumlah_peserta' => $item->jumlah_peserta,
                'status' => $item->status_booking,
                'metode_pembayaran' => $item->pembayaranTerakhir->metode_pembayaran ?? '-',
                'sisa_bayar' => $sisaBayar,
                'detail_url' => route('dashboard.user.detailpesanan', $item->id_booking),
                'payment_label' => $paymentLabel,
            ]);
        }


        if ($filter !== 'semua') {
            $riwayat = $riwayat->filter(function ($item) use ($filter) {
                return strtolower($item['jenis']) === strtolower($filter);
            })->values();
        }

        $riwayat = $riwayat->sortByDesc('tanggal')->values();

        return view('dashboard.user.riwayatbooking', [
            'riwayat' => $riwayat,
            'filter' => $filter,
            'page' => $page,
        ]);
    }
}