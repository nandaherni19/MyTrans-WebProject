<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Total pendapatan (semua pembayaran berhasil) ──
        $totalPendapatan = Pembayaran::whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->sum('jumlah_bayar');

        // ── Booking aktif ──
        $totalBookingAktif = Booking::where('status_booking', 'aktif')->count();

        // ── Total pelanggan (role user) ──
        $totalCustomer = User::where('role', 'user')->count();

        // Pelanggan baru bulan ini
        $customerBaru = User::where('role', 'user')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // ── Kendaraan tersedia hari ini ──
        $today = Carbon::today()->format('Y-m-d');

        $dipakaiIds = DB::table('tr_booking_kendaraan')
            ->join('ms_booking', 'tr_booking_kendaraan.id_booking', '=', 'ms_booking.id_booking')
            ->whereIn('ms_booking.status_booking', ['pending', 'aktif'])
            ->where('ms_booking.tanggal_berangkat', '<=', $today)
            ->where('ms_booking.tanggal_kembali', '>=', $today)
            ->pluck('tr_booking_kendaraan.id_kendaraan')
            ->unique()
            ->toArray();

        $totalKendaraan = Kendaraan::count();
        $kendaraanTersedia = $totalKendaraan - count($dipakaiIds);

        // ── Kendaraan perlu perawatan (tidak ada field khusus, set 0) ──
        $kendaraanPerluPerawatan = 0;

        // ── Chart bulanan: total pembayaran berhasil per bulan (tahun ini) ──
        $tahunIni = Carbon::now()->year;

        $pendapatanBulanan = Pembayaran::whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->whereYear('created_at', $tahunIni)
            ->selectRaw('MONTH(created_at) as bulan, SUM(jumlah_bayar) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartMonthly = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartMonthly[] = (int) ($pendapatanBulanan[$i] ?? 0);
        }

        // ── Chart harian: 7 hari terakhir ──
        $pendapatanHarian = Pembayaran::whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('DAYOFWEEK(created_at) as hari, SUM(jumlah_bayar) as total')
            ->groupBy('hari')
            ->pluck('total', 'hari')
            ->toArray();

        // DAYOFWEEK: 1=Minggu, 2=Sen, ..., 7=Sab → kita map ke Sen-Min
        $chartDaily = [];
        foreach ([2, 3, 4, 5, 6, 7, 1] as $dow) {
            $chartDaily[] = (int) ($pendapatanHarian[$dow] ?? 0);
        }

        // ── Paket wisata terlaris ──
        $paketTerlaris = Booking::with('paket')
            ->select('id_paket', DB::raw('count(*) as total_booking'))
            ->whereNotNull('id_paket')
            ->groupBy('id_paket')
            ->orderByDesc('total_booking')
            ->first();

        // ── 5 booking terbaru ──
        $recentBookings = Booking::with(['user', 'paket'])
            ->select('*')  // ← pastikan select semua kolom
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.beranda-admin', compact(
            'totalPendapatan',
            'totalBookingAktif',
            'totalCustomer',
            'totalKendaraan',
            'kendaraanTersedia',
            'kendaraanPerluPerawatan',
            'chartMonthly',
            'chartDaily',
            'recentBookings',
            'customerBaru',
            'paketTerlaris'
        ));


    }
}