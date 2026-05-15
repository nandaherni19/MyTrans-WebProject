@extends('layouts.admin')
@section('title', 'Kelola Laporan Transaksi')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/kelola-laporan-transaksi.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
@endpush

@section('content')
    <div class="laporan-topbar">
        <div class="laporan-title">
            <h1>Laporan Transaksi</h1>
            <p>Kelola Laporan Transaksi</p>
        </div>

        <div class="laporan-filter">
            <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="searchTransaksi" placeholder="Cari ID, nama, paket...">
            </div>
        </div>

    </div>

    <div class="main-scroll">
        <section class="laporan-wrapper">

            {{-- PERIODE BOX --}}
            <div class="periode-box">
                <h3 class="section-mini-title">🗓 Periode Laporan</h3>
                <div class="periode-row">
                    <div class="periode-group">
                        <label>Dari Tanggal</label>
                        <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ $dariTanggal ?? '' }}">
                    </div>
                    <div class="periode-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ $sampaiTanggal ?? '' }}">
                    </div>

                    <div class="periode-action">
                        <button type="button" class="btn-export" onclick="exportXLS()">
                            ⬇ Export XLS
                        </button>
                        <button type="button" class="btn-export" style="background:#ef4444;" onclick="exportPDF()">
                            ⬇ Export PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="summary-grid">
                <div class="summary-card summary-green">
                    <div>
                        <p class="summary-icon"><i data-lucide="wallet"></i></p>
                        <p class="summary-label">Total Pendapatan</p>
                        <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <span>{{ $jumlahPendapatan }} transaksi selesai</span>
                </div>

                <div class="summary-card summary-yellow">
                    <div>
                        <p class="summary-icon"><i data-lucide="clock"></i></p>
                        <p class="summary-label">Menunggu / Belum Selesai</p>
                        <h3>Rp {{ number_format($totalMenunggu, 0, ',', '.') }}</h3>
                    </div>
                    <span>{{ $jumlahMenunggu }} transaksi belum selesai</span>
                </div>

                <div class="summary-card summary-blue">
                    <div>
                        <p class="summary-icon"><i data-lucide="credit-card"></i></p>
                        <p class="summary-label">Total DP Diterima</p>
                        <h3>Rp {{ number_format($totalDp, 0, ',', '.') }}</h3>
                    </div>
                    <span>{{ $jumlahDp }} transaksi DP</span>
                </div>

                <div class="summary-card summary-red">
                    <div>
                        <p class="summary-icon"><i data-lucide="undo-2"></i></p>
                        <p class="summary-label">Total Refund</p>
                        <h3>Rp {{ number_format($totalRefund, 0, ',', '.') }}</h3>
                    </div>
                    <span>Refund yang sudah ditransfer</span>
                </div>

                <div class="summary-card summary-purple">
                    <div>
                        <p class="summary-icon"><i data-lucide="trending-up"></i></p>
                        <p class="summary-label">Pendapatan Bersih</p>
                        <h3>Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</h3>
                    </div>
                    <span>Pembayaran berhasil - refund</span>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI --}}
            <div class="riwayat-box">
                <h3 class="riwayat-title">Riwayat Transaksi</h3>

                <div class="table-wrapper">
                    <table class="laporan-table">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Pelanggan</th>
                                <th>Paket</th>
                                <th>Tanggal</th>
                                <th>Tgl Berangkat</th>
                                <th>Total Harga</th>
                                <th>Dibayar</th>
                                <th>Status</th>
                                <th>Status Booking</th> {{-- ← tambah --}}
                                <th>Refund</th> {{-- ← tambah --}}
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                                    @php
                                                        $pembayaranTerakhir = $booking->pembayarans->sortByDesc('id_pembayaran')->first();
                                                        $status = optional($pembayaranTerakhir)->transaction_status ?? 'pending';
                                                        $badgeClass = match ($status) {
                                                            'berhasil', 'settlement', 'capture' => 'badge-success',
                                                            'pending' => 'badge-pending',
                                                            'expired', 'gagal', 'cancel' => 'badge-danger',
                                                            default => 'badge-gray',
                                                        };
                                                        $statusLabel = match ($status) {
                                                            'berhasil' => 'Berhasil',
                                                            'settlement' => 'Settlement',
                                                            'capture' => 'Capture',
                                                            'pending' => 'Pending',
                                                            'expired' => 'Expired',
                                                            'gagal' => 'Gagal',
                                                            'cancel' => 'Dibatalkan',
                                                            default => ucfirst($status),
                                                        };

                                                        // Hitung sudah bayar
                                                        $sudahBayar = $booking->pembayarans
                                                            ->whereIn('transaction_status', ['berhasil', 'settlement', 'capture'])
                                                            ->sum('jumlah_bayar');

                                                        // Cek refund
                                                        $refundSelesai = $booking->pembayarans->where('status_refund', 'selesai')->first();
                                                        $refundDiproses = $booking->pembayarans->whereIn('status_refund', ['pending', 'diproses'])->first();
                                                        $estimasiRefund = floor($sudahBayar * 0.85);
                                                    @endphp
                                                    <tr class="transaksi-row" data-search="{{ strtolower(
                                    'BK' . str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) . ' ' .
                                    ($booking->pelanggan->nama ?? '') . ' ' .
                                    ($booking->paket->nama_paket ?? '') . ' ' .
                                    $status . ' ' .
                                    (optional($pembayaranTerakhir)->metode_pembayaran ?? '')
                                ) }}">

                                                        <td style="font-weight: 600; color: #0f172a;">
                                                            BK{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}
                                                        </td>
                                                        <td>{{ $booking->pelanggan->nama ?? '-' }}</td>
                                                        <td style="color: #475569;">{{ $booking->paket->nama_paket ?? '-' }}</td>
                                                        <td style="color: #64748b;">
                                                            {{ $booking->created_at
                                                                    ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y')
                                                                    : '-' }}
                                                        </td>

                                                         <td style="color: #64748b;">
                                                            {{ $booking->tanggal_berangkat
                                                                ? $booking->tanggal_berangkat->format('d/m/Y')
                                                                : '-' }}
                                                        </td>

                                                        <td style="font-weight: 500;">
                                                            Rp {{ number_format($booking->total_biaya ?? 0, 0, ',', '.') }}
                                                        </td>

                                                        <td style="font-weight: 500; color: #15803d;">
                                                            Rp {{ number_format($sudahBayar, 0, ',', '.') }}
                                                        </td>

                                                        {{-- Status Pembayaran --}}
                                                        <td>
                                                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                                        </td>

                                                        {{-- Status Booking --}}
                                                        <td>
                                                            @php
                                                                $statusBooking = $booking->status_booking ?? '-';
                                                                $bookingBadge = match ($statusBooking) {
                                                                    'aktif' => ['class' => 'badge-success', 'label' => 'Aktif'],
                                                                    'pending' => ['class' => 'badge-pending', 'label' => 'Pending'],
                                                                    'selesai' => ['class' => 'badge-gray', 'label' => 'Selesai'],
                                                                    'batal' => ['class' => 'badge-danger', 'label' => 'Dibatalkan'],
                                                                    default => ['class' => 'badge-gray', 'label' => ucfirst($statusBooking)],
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $bookingBadge['class'] }}">{{ $bookingBadge['label'] }}</span>
                                                        </td>

                                                        {{-- Refund --}}
                                                        <td>
                                                            @if($booking->status_booking !== 'batal')
                                                                <span style="color: #cbd5e1;">-</span>
                                                            @elseif($refundSelesai)
                                                                {{-- Refund sudah ditransfer --}}
                                                                <div>
                                                                    <span style="color: #dc2626; font-weight: 600; display:block;">
                                                                        Rp {{ number_format($refundSelesai->jumlah_refund, 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="badge badge-success" style="margin-top:4px;">Sudah Ditransfer</span>
                                                                </div>
                                                            @elseif($refundDiproses)
                                                                {{-- Refund sedang diproses --}}
                                                                <div>
                                                                    <span style="color: #b45309; font-weight: 600; display:block;">
                                                                        Rp
                                                                        {{ number_format($refundDiproses->jumlah_refund ?? $estimasiRefund, 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="badge badge-pending" style="margin-top:4px;">Diproses</span>
                                                                </div>
                                                            @elseif($sudahBayar > 0)
                                                                {{-- Sudah bayar, belum ada proses refund --}}
                                                                <div>
                                                                    <span style="color: #94a3b8; font-size: 11px; display:block;">Belum diproses</span>
                                                                    <span style="color: #f59e0b; font-size: 11px;">
                                                                        Est. Rp {{ number_format($estimasiRefund, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                            @else
                                                                {{-- Belum ada pembayaran, tidak ada refund --}}
                                                                <span style="color: #94a3b8; font-size: 12px;">Tidak ada refund</span>
                                                            @endif
                                                        </td>

                                                        <td
                                                            style="color: #475569; text-transform: uppercase; font-size: 12px; font-weight: 600; letter-spacing: 0.3px;">
                                                            {{ optional($pembayaranTerakhir)->metode_pembayaran ?? '-' }}
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align:center; color:#94a3b8; padding:40px 0;">
                                        Belum ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            const input = document.getElementById('searchTransaksi');
            const rows = document.querySelectorAll('.transaksi-row');

            input?.addEventListener('input', function () {
                const kw = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    row.style.display = row.dataset.search.includes(kw) ? '' : 'none';
                });
            });
            // AUTO FILTER TANGGAL
            document.getElementById('dari_tanggal')
                ?.addEventListener('change', filterTanggal);

            document.getElementById('sampai_tanggal')
                ?.addEventListener('change', filterTanggal);
        });

        function exportXLS() {
            const dari = document.getElementById('dari_tanggal').value;
            const sampai = document.getElementById('sampai_tanggal').value;

            let url = "{{ route('dashboard.superadmin.kelola-laporan-transaksi.export-xls') }}";

            if (dari && sampai) {
                url += '?dari_tanggal=' + encodeURIComponent(dari)
                    + '&sampai_tanggal=' + encodeURIComponent(sampai);
            }

            window.location.href = url;
        }

        function exportPDF() {
            const dari = document.getElementById('dari_tanggal').value;
            const sampai = document.getElementById('sampai_tanggal').value;
            let url = "{{ route('dashboard.superadmin.kelola-laporan-transaksi.export-pdf') }}";
            if (dari && sampai) {
                url += '?dari_tanggal=' + encodeURIComponent(dari) + '&sampai_tanggal=' + encodeURIComponent(sampai);
            }
            window.location.href = url;
        }


        function filterTanggal() {

            const dari = document.getElementById('dari_tanggal').value;
            const sampai = document.getElementById('sampai_tanggal').value;

            let url = "{{ route('dashboard.superadmin.kelola-laporan-transaksi') }}";

            if (dari && sampai) {

                url += '?dari_tanggal=' + encodeURIComponent(dari)
                    + '&sampai_tanggal=' + encodeURIComponent(sampai);
            }

            window.location.href = url;
        }
    </script>
@endpush