<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->query('dari_tanggal');
        $sampaiTanggal = $request->query('sampai_tanggal');

        $query = Booking::with(['pelanggan', 'paket', 'pembayarans']);

        if ($dariTanggal && $sampaiTanggal) {
            $query->whereDate('created_at', '>=', $dariTanggal)
                ->whereDate('created_at', '<=', $sampaiTanggal);
        }

        $bookings = $query->orderByDesc('created_at')->get();

         // ===== TOTAL PENDAPATAN (lunas, berhasil, tidak batal) =====
        $totalPendapatan = $bookings->sum(function ($booking) {
            if ($booking->status_booking === 'batal') return 0;
            if ($booking->opsi_pembayaran !== 'lunas') return 0;

            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $jumlahPendapatan = $bookings->filter(function ($booking) {
            return $booking->status_booking !== 'batal'
                && $booking->opsi_pembayaran === 'lunas'
                && $booking->pembayarans->where('transaction_status', 'berhasil')->count() > 0;
        })->count();

        // ===== TOTAL DP DITERIMA (dp, berhasil, tidak batal) =====
        $totalDp = $bookings->sum(function ($booking) {
            if ($booking->status_booking === 'batal') return 0;
            if ($booking->opsi_pembayaran !== 'dp') return 0;

            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $jumlahDp = $bookings->filter(function ($booking) {
            return $booking->status_booking !== 'batal'
                && $booking->opsi_pembayaran === 'dp'
                && $booking->pembayarans->where('transaction_status', 'berhasil')->count() > 0;
        })->count();

        // ===== TOTAL REFUND SELESAI =====
        $totalRefund = $bookings->sum(function ($booking) {
            return $booking->pembayarans
                ->where('status_refund', 'selesai')
                ->sum('jumlah_refund');
        });

        $jumlahBatal  = $bookings->where('status_booking', 'batal')->count();
        $nominalBatal = $bookings->where('status_booking', 'batal')->sum('total_biaya');

        // Refund yang sudah selesai (untuk label jumlah)
        $jumlahRefund = $bookings->sum(function ($booking) {
            return $booking->pembayarans->where('status_refund', 'selesai')->count();
        });

        // ===== PENDAPATAN BERSIH =====
        // Lunas + DP yang masuk - refund yang harus dikembalikan
        $totalMasuk = $bookings->sum(function ($booking) {
            return $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $pendapatanBersih = $totalMasuk - $totalRefund;

        // ===== MENUNGGU =====
        $bookingsMenunggu = $bookings->filter(function ($booking) {
            if ($booking->status_booking === 'batal') return false;

            $pembayaranTerakhir = $booking->pembayarans
                ->sortByDesc('id_pembayaran')
                ->first();

            $statusTerakhir = optional($pembayaranTerakhir)->transaction_status;

            if (in_array($statusTerakhir, ['expired', 'gagal'])) return false;

            return $booking->status_booking === 'pending'
                || $booking->opsi_pembayaran === 'dp'
                || $statusTerakhir === 'pending'
                || $booking->pembayarans->sum('jumlah_bayar') < ($booking->total_biaya ?? 0);
        });

        $totalMenunggu = $bookingsMenunggu->sum(function ($booking) {
            $sudahBayar = $booking->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
            return max(0, ($booking->total_biaya ?? 0) - $sudahBayar);
        });

        $jumlahMenunggu = $bookingsMenunggu->count();

        return view('dashboard.superadmin.kelola-laporan-transaksi', compact(
            'bookings',
            'totalPendapatan',
            'jumlahPendapatan',
            'totalDp',
            'jumlahDp',
            'totalMenunggu',
            'jumlahMenunggu',
            'totalRefund',
            'jumlahBatal',
            'nominalBatal',   // ← tambah
            'jumlahRefund',   // ← tambah
            'pendapatanBersih',
            'dariTanggal',
            'sampaiTanggal'
        ));
    }

    public function exportCsv(Request $request)
{
    $dariTanggal = $request->query('dari_tanggal');
    $sampaiTanggal = $request->query('sampai_tanggal');

    $query = Booking::with(['pelanggan', 'paket', 'pembayarans']);

    if ($dariTanggal && $sampaiTanggal) {
        $query->whereDate('created_at', '>=', $dariTanggal)
            ->whereDate('created_at', '<=', $sampaiTanggal);
    }

    $rows = $query->orderByDesc('created_at')->get();

    $fileName = 'laporan-transaksi-' . now()->format('Y-m-d') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename={$fileName}",
    ];

    return response()->stream(function () use ($rows) {
        $handle = fopen('php://output', 'w');

        // UTF-8 BOM supaya Excel baca karakter dengan benar
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header kolom
        fputcsv($handle, [
            'ID Booking',
            'Pelanggan',
            'Paket',
            'Tanggal',
            'Total Harga',
            'Dibayar',
            'Status',
            'Metode'
        ], ';');

        foreach ($rows as $booking) {
            $pembayaranTerakhir = $booking->pembayarans
                ->sortByDesc('id_pembayaran')
                ->first();

            fputcsv($handle, [
                'BK' . str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT),
                optional($booking->pelanggan)->nama ?? '-',
                optional($booking->paket)->nama_paket ?? '-',
                optional($booking->created_at)?->format('d/m/Y') ?? '-',
                $booking->total_biaya ?? 0,
                $booking->pembayarans->sum('jumlah_bayar'),
                optional($pembayaranTerakhir)->transaction_status ?? '-',
                strtoupper(optional($pembayaranTerakhir)->metode_pembayaran ?? '-'),
            ], ';');
        }

        fclose($handle);
    }, 200, $headers);
}
}