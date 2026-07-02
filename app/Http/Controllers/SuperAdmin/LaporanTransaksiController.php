<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->query('dari_tanggal') ?? now()->toDateString();
        $sampaiTanggal = $request->query('sampai_tanggal') ?? now()->toDateString();

        $query = Booking::with([
            'pelanggan',
            'paket',
            'pembayarans'
        ]);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereDate('created_at', '>=', $dariTanggal)
                ->whereDate('created_at', '<=', $sampaiTanggal);
        }

        $bookings = $query->orderByDesc('created_at')->get();

        // ===== TOTAL PENDAPATAN =====
        $totalPendapatan = $bookings->sum(function ($booking) {

            if ($booking->status_booking === 'batal')
                return 0;

            if ($booking->opsi_pembayaran !== 'lunas')
                return 0;

            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $jumlahPendapatan = $bookings->filter(function ($booking) {

            return $booking->status_booking !== 'batal'
                && $booking->opsi_pembayaran === 'lunas'
                && $booking->pembayarans
                    ->where('transaction_status', 'berhasil')
                    ->count() > 0;
        })->count();

        // ===== TOTAL DP =====
        $totalDp = $bookings->sum(function ($booking) {

            if ($booking->status_booking === 'batal')
                return 0;

            if ($booking->opsi_pembayaran !== 'dp')
                return 0;

            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $jumlahDp = $bookings->filter(function ($booking) {

            return $booking->status_booking !== 'batal'
                && $booking->opsi_pembayaran === 'dp'
                && $booking->pembayarans
                    ->where('transaction_status', 'berhasil')
                    ->count() > 0;
        })->count();

        // ===== TOTAL REFUND =====
        $totalRefund = $bookings->sum(function ($booking) {

            return $booking->pembayarans
                ->where('status_refund', 'selesai')
                ->sum('jumlah_refund');
        });

        $jumlahBatal = $bookings
            ->where('status_booking', 'batal')
            ->count();

        $nominalBatal = $bookings
            ->where('status_booking', 'batal')
            ->sum('total_biaya');

        $jumlahRefund = $bookings->sum(function ($booking) {

            return $booking->pembayarans
                ->where('status_refund', 'selesai')
                ->count();
        });

        // ===== PENDAPATAN BERSIH =====
        $totalMasuk = $bookings->sum(function ($booking) {

            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $pendapatanBersih = $totalMasuk - $totalRefund;

        // ===== MENUNGGU =====
        $bookingsMenunggu = $bookings->filter(function ($booking) {

            if ($booking->status_booking === 'batal') {
                return false;
            }

            $pembayaranTerakhir = $booking->pembayarans
                ->sortByDesc('id_pembayaran')
                ->first();

            $statusTerakhir = optional($pembayaranTerakhir)
                ->transaction_status;

            if (in_array($statusTerakhir, ['expired', 'gagal'])) {
                return false;
            }

            return $booking->status_booking === 'pending'
                || $booking->opsi_pembayaran === 'dp'
                || $statusTerakhir === 'pending'
                || $booking->pembayarans->sum('jumlah_bayar')
                < ($booking->total_biaya ?? 0);
        });

        $totalMenunggu = $bookingsMenunggu->sum(function ($booking) {

            $sudahBayar = $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');

            return max(
                0,
                ($booking->total_biaya ?? 0) - $sudahBayar
            );
        });

        $jumlahMenunggu = $bookingsMenunggu->count();

        return view(
            'dashboard.superadmin.kelola-laporan-transaksi',
            compact(
                'bookings',
                'totalPendapatan',
                'jumlahPendapatan',
                'totalDp',
                'jumlahDp',
                'totalMenunggu',
                'jumlahMenunggu',
                'totalRefund',
                'jumlahBatal',
                'nominalBatal',
                'jumlahRefund',
                'pendapatanBersih',
                'dariTanggal',
                'sampaiTanggal'
            )
        );
    }

    // ===== EXPORT XLSX =====
    public function exportXls(Request $request)
    {
        $dariTanggal = $request->query('dari_tanggal');
        $sampaiTanggal = $request->query('sampai_tanggal');

        $query = Booking::with(['pelanggan', 'paket', 'pembayarans']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('created_at', [$dariTanggal . ' 00:00:00', $sampaiTanggal . ' 23:59:59']);
        }

        $bookings = $query->orderByDesc('created_at')->get();

        $filename = 'laporan-transaksi-' . now()->format('Y-m-d') . '.xls';

        return response()->view('dashboard.superadmin.exports.laporan-transaksi-xls', compact('bookings', 'dariTanggal', 'sampaiTanggal'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ===== EXPORT PDF =====
    public function exportPdf(Request $request)
    {
        $dariTanggal = $request->query('dari_tanggal');
        $sampaiTanggal = $request->query('sampai_tanggal');

        $query = Booking::with([
            'pelanggan',
            'paket',
            'pembayarans'
        ]);

        if ($dariTanggal && $sampaiTanggal) {

            $query->whereDate('created_at', '>=', $dariTanggal)
                ->whereDate('created_at', '<=', $sampaiTanggal);
        }

        $bookings = $query->orderByDesc('created_at')->get();

        $totalPendapatan = $bookings->sum(function ($b) {

            if (
                $b->status_booking === 'batal' ||
                $b->opsi_pembayaran !== 'lunas'
            ) {
                return 0;
            }

            return $b->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $totalDp = $bookings->sum(function ($b) {

            if (
                $b->status_booking === 'batal' ||
                $b->opsi_pembayaran !== 'dp'
            ) {
                return 0;
            }

            return $b->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $totalRefund = $bookings->sum(function ($b) {

            return $b->pembayarans
                ->where('status_refund', 'selesai')
                ->sum('jumlah_refund');
        });

        $totalMasuk = $bookings->sum(function ($b) {

            return $b->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $totalMenunggu = $bookings->filter(function ($b) {
            if ($b->status_booking === 'batal') return false;
            $last = $b->pembayarans->sortByDesc('id_pembayaran')->first();
            $statusTerakhir = optional($last)->transaction_status;
            if (in_array($statusTerakhir, ['expired', 'gagal'])) return false;
            return $b->status_booking === 'pending'
                || $b->opsi_pembayaran === 'dp'
                || $statusTerakhir === 'pending'
                || $b->pembayarans->sum('jumlah_bayar') < ($b->total_biaya ?? 0);
        })->sum(function ($b) {
            $sudahBayar = $b->pembayarans
                ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                ->sum('jumlah_bayar');
            return max(0, ($b->total_biaya ?? 0) - $sudahBayar);
        });

$pendapatanBersih = $totalMasuk - $totalRefund;

$fileName = 'laporan-transaksi-' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView(
            'dashboard.superadmin.exports.laporan-transaksi-pdf',
            compact(
                'bookings',
                'totalPendapatan',
                'totalDp',
                'totalMenunggu',
                'totalRefund',
                'pendapatanBersih',
                'dariTanggal',
                'sampaiTanggal'
            )
        )->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}