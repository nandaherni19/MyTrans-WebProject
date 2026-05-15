<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        body{
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        h1 {
            font-size: 14px;
            text-align: center;
            color: #4f46e5;
            margin-bottom: 4px;
        }

        .sub {
            font-size: 9px;
            text-align: center;
            color: #64748b;
            margin-bottom: 12px;
        }

        /* =========================
           SUMMARY
        ========================== */

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .summary-table tr {
            width: 100%;
        }

        .summary-table td {
            padding: 10px;
            border: 1px solid #dbeafe;
            vertical-align: top;
        }

        /* Warna per card */

        .s-green {
            border-top: 4px solid #16a34a;
            background: #f0fdf4;
        }

        .s-blue {
            border-top: 4px solid #2563eb;
            background: #eff6ff;
        }

        .s-orange {
            border-top: 4px solid #ea580c;
            background: #fff7ed;
        }

        .s-red {
            border-top: 4px solid #dc2626;
            background: #fef2f2;
        }

        .s-purple {
            border-top: 4px solid #7c3aed;
            background: #f5f3ff;
        }

        .s-label {
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .s-value {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            word-wrap: break-word;
        }

        /* Warna nilai */

        .s-green .s-value {
            color: #16a34a;
        }

        .s-blue .s-value {
            color: #2563eb;
        }

        .s-orange .s-value {
            color: #ea580c;
        }

        .s-red .s-value {
            color: #dc2626;
        }

        .s-purple .s-value {
            color: #7c3aed;
        }

        /* =========================
           DATA TABLE
        ========================== */

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th,
        table.data-table td{
            white-space: nowrap;
        }

        table.data-table thead tr {
            background: #4f46e5;
            color: #fff;
        }

        table.data-table thead th {
            padding: 7px 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #3730a3;
            word-wrap: break-word;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.data-table tbody td {
            padding: 6px 8px;
            font-size: 9px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .footer {
            margin-top: 12px;
            font-size: 8px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <h1>Laporan Transaksi</h1>

    <div class="sub">
        @if($dariTanggal && $sampaiTanggal)
            Periode:
            {{ \Carbon\Carbon::parse($dariTanggal)->format('d/m/Y') }}
            s/d
            {{ \Carbon\Carbon::parse($sampaiTanggal)->format('d/m/Y') }}
        @else
            Semua Periode
        @endif

        &nbsp;&bull;&nbsp;

        Dicetak:
        {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- SUMMARY --}}
    @php
        $totalPendapatan = $bookings->sum(function ($b) {
            if ($b->status_booking === 'batal' || $b->opsi_pembayaran !== 'lunas')
                return 0;

            return $b->pembayarans
                ->where('transaction_status', 'berhasil')
                ->sum('jumlah_bayar');
        });

        $totalDp = $bookings->sum(function ($b) {
            if ($b->status_booking === 'batal' || $b->opsi_pembayaran !== 'dp')
                return 0;

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

        $pendapatanBersih = $totalMasuk - $totalRefund;

        $totalMenunggu = $bookings
            ->filter(function ($b) {

                if ($b->status_booking === 'batal')
                    return false;

                $last = $b->pembayarans
                    ->sortByDesc('id_pembayaran')
                    ->first();

                $statusTerakhir = optional($last)->transaction_status;

                if (in_array($statusTerakhir, ['expired', 'gagal']))
                    return false;

                return $b->status_booking === 'pending'
                    || $b->opsi_pembayaran === 'dp'
                    || $statusTerakhir === 'pending'
                    || $b->pembayarans->sum('jumlah_bayar') < ($b->total_biaya ?? 0);
            })
            ->sum(function ($b) {

                $sudahBayar = $b->pembayarans
                    ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                    ->sum('jumlah_bayar');

                return max(0, ($b->total_biaya ?? 0) - $sudahBayar);
            });
    @endphp

    <table class="summary-table">
        <tr>

            <td class="s-green">
                <div class="s-label">
                    💰 Total Pendapatan
                </div>

                <div class="s-value">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>
            </td>

            <td class="s-blue">
                <div class="s-label">
                    💳 Total DP Diterima
                </div>

                <div class="s-value">
                    Rp {{ number_format($totalDp, 0, ',', '.') }}
                </div>
            </td>

            <td class="s-orange">
                <div class="s-label">
                    ⏳ Menunggu / Belum Selesai
                </div>

                <div class="s-value">
                    Rp {{ number_format($totalMenunggu, 0, ',', '.') }}
                </div>
            </td>

            <td class="s-red">
                <div class="s-label">
                    ↩ Total Refund
                </div>

                <div class="s-value">
                    Rp {{ number_format($totalRefund, 0, ',', '.') }}
                </div>
            </td>

            <td class="s-purple">
                <div class="s-label">
                    📈 Pendapatan Bersih
                </div>

                <div class="s-value">
                    Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}
                </div>
            </td>

        </tr>
    </table>

    {{-- DATA TABLE --}}
    <table class="data-table">

        <thead>
            <tr>
                <th>#</th>
                <th>ID Booking</th>
                <th>Pelanggan</th>
                <th>Paket</th>
                <th>Tanggal</th>
                <th>Tgl Berangkat</th>
                <th>Total Harga</th>
                <th>Dibayar</th>
                <th>Status Bayar</th>
                <th>Status Booking</th>
                <th>Refund</th>
                <th>Metode</th>
            </tr>
        </thead>

        <tbody>

            @forelse($bookings as $i => $booking)

                @php

                    $pembayaran = $booking->pembayarans
                        ->sortByDesc('id_pembayaran')
                        ->first();

                    $status = optional($pembayaran)->transaction_status ?? 'pending';

                    $statusLabel = match ($status) {
                        'berhasil', 'settlement', 'capture' => 'Berhasil',
                        'pending' => 'Pending',
                        'expired', 'gagal', 'cancel' => 'Gagal',
                        default => ucfirst($status),
                    };

                    $bookingLabel = match ($booking->status_booking ?? '') {
                        'aktif' => 'Aktif',
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                        'batal' => 'Dibatalkan',
                        default => '-',
                    };

                    $sudahBayar = $booking->pembayarans
                        ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                        ->sum('jumlah_bayar');

                    $refundSelesai = $booking->pembayarans
                        ->where('status_refund', 'selesai')
                        ->first();

                @endphp

                <tr>

                    <td style="text-align:center; color:#94a3b8;">
                        {{ $i + 1 }}
                    </td>

                    <td style="font-weight:bold; color:#4f46e5;">
                        BK{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    <td>
                        {{ $booking->pelanggan->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $booking->paket->nama_paket ?? '-' }}
                    </td>

                    <td>
                        {{ optional($booking->created_at)->format('d/m/Y') }}
                    </td>

                    <td>  {{-- ← tambah ini --}}
                        {{ $booking->tanggal_berangkat
                            ? $booking->tanggal_berangkat->format('d/m/Y')
                            : '-' }}
                    </td>

                    <td style="font-weight:bold;">
                        Rp {{ number_format($booking->total_biaya ?? 0, 0, ',', '.') }}
                    </td>

                    <td style="font-weight:bold; color:#15803d;">
                        Rp {{ number_format($sudahBayar, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $statusLabel }}
                    </td>

                    <td>
                        {{ $bookingLabel }}
                    </td>

                    <td>
                        @if($refundSelesai)
                            Rp {{ number_format($refundSelesai->jumlah_refund, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>

                    <td style="text-transform:uppercase; font-weight:bold;">
                        {{ optional($pembayaran)->metode_pembayaran ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="11"
                        style="text-align:center; padding:20px; color:#94a3b8;">
                        Tidak ada data transaksi.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    {{-- FOOTER --}}
    <div class="footer">

        <span>
            MyTrans &mdash; Sistem Manajemen Wisata
        </span>

        <span>
            Total {{ $bookings->count() }} transaksi
            &nbsp;|&nbsp;
            Dicetak {{ now()->format('d/m/Y H:i') }}
        </span>

    </div>

</body>

</html>