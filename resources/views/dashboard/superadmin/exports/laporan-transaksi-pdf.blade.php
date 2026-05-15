<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
            padding: 20px 24px;
        }

        /* ── HEADER ── */
        .header {
            text-align: center;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 0.5px;
        }

        .header .sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }

        .divider {
            border: none;
            border-top: 2px solid #4f46e5;
            margin: 10px 0 14px;
        }

        /* ── SUMMARY GRID ── */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 16px;
        }

        .summary-table td {
            width: 20%;
            padding: 10px 12px;
            border-radius: 6px;
            vertical-align: top;
        }

        .s-green {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
        }

        .s-blue {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
        }

        .s-orange {
            background: #fff7ed;
            border-left: 4px solid #ea580c;
        }

        .s-red {
            background: #fff1f2;
            border-left: 4px solid #ef4444;
        }

        .s-purple {
            background: #f5f3ff;
            border-left: 4px solid #8b5cf6;
        }

        .s-label {
            font-size: 7.5px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .s-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 6px;
            border-left: 3px solid #4f46e5;
            padding-left: 8px;
        }

        /* ── TABLE ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table thead tr {
            background: #4f46e5;
            color: #fff;
        }

        table.data-table thead th {
            padding: 7px 8px;
            font-size: 8px;
            font-weight: 600;
            text-align: left;
            letter-spacing: 0.2px;
        }

        table.data-table thead th:first-child {
            border-radius: 4px 0 0 0;
        }

        table.data-table thead th:last-child {
            border-radius: 0 4px 0 0;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.data-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        table.data-table tbody td {
            padding: 6px 8px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 7.5px;
            font-weight: 700;
        }

        .b-success {
            background: #dcfce7;
            color: #15803d;
        }

        .b-pending {
            background: #fef9c3;
            color: #a16207;
        }

        .b-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .b-gray {
            background: #f1f5f9;
            color: #475569;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>Laporan Transaksi</h1>
        <div class="sub">
            @if($dariTanggal && $sampaiTanggal)
                Periode: {{ \Carbon\Carbon::parse($dariTanggal)->format('d/m/Y') }}
                s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
            &nbsp;&bull;&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    <hr class="divider">

    {{-- SUMMARY CARDS --}}
    <table class="summary-table">
        <tr>
            <td class="s-green">
                <div class="s-label">&#128176; Total Pendapatan</div>
                <div class="s-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </td>
            <td class="s-blue">
                <div class="s-label">&#128179; Total DP Diterima</div>
                <div class="s-value">Rp {{ number_format($totalDp, 0, ',', '.') }}</div>
            </td>
            <td class="s-orange">
                <div class="s-label">&#9203; Menunggu / Belum Selesai</div>
                <div class="s-value">Rp {{ number_format($totalMenunggu, 0, ',', '.') }}</div>
            </td>
            <td class="s-red">
                <div class="s-label">&#8617; Total Refund</div>
                <div class="s-value">Rp {{ number_format($totalRefund, 0, ',', '.') }}</div>
            </td>
            <td class="s-purple">
                <div class="s-label">&#128200; Pendapatan Bersih</div>
                <div class="s-value">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION TITLE --}}
    <div class="section-title">Riwayat Transaksi</div>

    {{-- DATA TABLE --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:20px">#</th>
                <th style="width:55px">ID Booking</th>
                <th style="width:100px">Pelanggan</th>
                <th>Paket</th>
                <th style="width:60px">Tanggal</th>
                <th style="width:60px">Tgl Berangkat</th>
                <th style="width:80px">Total Harga</th>
                <th style="width:80px">Dibayar</th>
                <th style="width:65px">Status Bayar</th>
                <th style="width:65px">Status Booking</th>
                <th style="width:75px">Refund</th>
                <th style="width:45px">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $i => $booking)
                @php
                    $pembayaran = $booking->pembayarans->sortByDesc('id_pembayaran')->first();
                    $status = optional($pembayaran)->transaction_status ?? 'pending';

                    $statusInfo = match ($status) {
                        'berhasil', 'settlement', 'capture' => ['label' => 'Berhasil', 'badge' => 'b-success'],
                        'pending' => ['label' => 'Pending', 'badge' => 'b-pending'],
                        'expired', 'gagal', 'cancel' => ['label' => 'Gagal', 'badge' => 'b-danger'],
                        default => ['label' => ucfirst($status), 'badge' => 'b-gray'],
                    };

                    $bookingInfo = match ($booking->status_booking ?? '') {
                        'aktif' => ['label' => 'Aktif', 'badge' => 'b-success'],
                        'pending' => ['label' => 'Pending', 'badge' => 'b-pending'],
                        'selesai' => ['label' => 'Selesai', 'badge' => 'b-gray'],
                        'batal' => ['label' => 'Dibatalkan', 'badge' => 'b-danger'],
                        default => ['label' => '-', 'badge' => 'b-gray'],
                    };

                    $sudahBayar = $booking->pembayarans
                        ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                        ->sum('jumlah_bayar');
                    $refundSelesai = $booking->pembayarans->where('status_refund', 'selesai')->first();
                @endphp
                <tr>
                    <td style="color:#94a3b8; text-align:center;">{{ $i + 1 }}</td>
                    <td style="font-weight:700; color:#4f46e5;">
                        BK{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>{{ $booking->pelanggan->nama ?? '-' }}</td>
                    <td style="color:#475569;">{{ $booking->paket->nama_paket ?? '-' }}</td>
                    <td style="color:#64748b;">{{ optional($booking->created_at)->format('d/m/Y') }}</td>
                    <td style="color:#64748b;">
                        {{ $booking->tanggal_berangkat ? $booking->tanggal_berangkat->format('d/m/Y') : '-' }}
                    </td>
                    <td style="font-weight:600;">Rp {{ number_format($booking->total_biaya ?? 0, 0, ',', '.') }}</td>
                    <td style="font-weight:600; color:#15803d;">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</td>
                    <td><span class="badge {{ $statusInfo['badge'] }}">{{ $statusInfo['label'] }}</span></td>
                    <td><span class="badge {{ $bookingInfo['badge'] }}">{{ $bookingInfo['label'] }}</span></td>
                    <td>
                        @if($refundSelesai)
                            <span style="color:#dc2626; font-weight:600;">
                                Rp {{ number_format($refundSelesai->jumlah_refund, 0, ',', '.') }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="font-weight:700; text-transform:uppercase; color:#475569; font-size:8px;">
                        {{ optional($pembayaran)->metode_pembayaran ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center; padding:24px; color:#94a3b8;">
                        Tidak ada data transaksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <span>MyTrans &mdash; Sistem Manajemen Wisata</span>
        <span>Total {{ $bookings->count() }} transaksi &nbsp;|&nbsp; Dicetak {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>

</html>