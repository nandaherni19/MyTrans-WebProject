@extends('layouts.admin')
@section('title', 'Dashboard')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>Beranda Dashboard</h1>
        <p>Selamat datang kembali, kelola operasional MY Trans Nusa Anda hari ini.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}?page=index" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Booking
        </a>
        <a href="{{ route('dashboard.superadmin.kelola-laporan-transaksi') }}" class="btn-secondary">
            <i class="fa-solid fa-file-arrow-down"></i> Ekspor Laporan
        </a>
    </div>
</div>

{{-- ===== STAT CARDS ===== --}}
<div class="stats-grid">
    {{-- Total Pendapatan --}}
    <div class="stat-card blue">
        <div class="stat-card-top">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="stat-label">Total Pendapatan</span>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-value small">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-sub">
                <i class="fa-solid fa-circle-check"></i> Keseluruhan transaksi
            </div>
        </div>
    </div>

    {{-- Booking Aktif --}}
    <div class="stat-card orange">
        <div class="stat-card-top">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <span class="stat-label">Booking Aktif</span>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-value">{{ $totalBookingAktif ?? 0 }}</div>
            <div class="stat-sub">
                <i class="fa-solid fa-circle-xmark"></i> Tidak ada antrian hari ini
            </div>
        </div>
    </div>

    {{-- Total Pelanggan --}}
    <div class="stat-card purple">
        <div class="stat-card-top">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="stat-label">Total Pelanggan</span>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-value">{{ $totalCustomer ?? 0 }}</div>
            <div class="stat-sub">
                @if(($customerBaru ?? 0) > 0)
                    <i class="fa-solid fa-arrow-trend-up"></i> +{{ $customerBaru }} pelanggan baru
                @else
                    <i class="fa-solid fa-user-check"></i> Peningkatan internal 15%
                @endif
            </div>
        </div>
    </div>

    {{-- Kendaraan Tersedia --}}
    <div class="stat-card teal">
        <div class="stat-card-top">
            <div class="stat-icon-wrap">
                <i class="fa-solid fa-bus"></i>
            </div>
            <span class="stat-label">Kendaraan Tersedia</span>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-value">{{ $kendaraanTersedia ?? 0 }}/{{ $totalKendaraan ?? 0 }}</div>
            <div class="stat-sub">
                <i class="fa-solid fa-circle-check"></i> Seluruh armada siap beroperasi
            </div>
        </div>
    </div>
</div>

{{-- ===== MAIN GRID: CHART + RIGHT COL ===== --}}
<div class="dashboard-grid">

    {{-- Chart --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h3>Ringkasan Pendapatan</h3>
                <p>Analitik per forma keuangan tahun 2024</p>
            </div>
            <div class="chart-toggle">
                <button onclick="switchChart('bulanan')" id="btnBulanan" class="active">Bulanan</button>
                <button onclick="switchChart('harian')" id="btnHarian">Harian</button>
            </div>
        </div>
        <div class="chart-area">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="right-col">

        {{-- Aksi Cepat --}}
        <div class="quick-actions-card">
            <h3>Aksi Cepat</h3>

            <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}?page=index#tambah" class="action-btn">
                <div class="action-icon blue"><i class="fa-solid fa-plus"></i></div>
                <div class="action-label-wrap">
                    <span class="action-label">Tambah Booking</span>
                    <span class="action-desc">Buat pesanan baru cepat</span>
                </div>
                <i class="fa-solid fa-chevron-right action-arrow"></i>
            </a>

            <a href="{{ route('dashboard.superadmin.kelola-paket-wisata') }}" class="action-btn">
                <div class="action-icon purple"><i class="fa-solid fa-umbrella-beach"></i></div>
                <div class="action-label-wrap">
                    <span class="action-label">Paket Wisata Baru</span>
                    <span class="action-desc">Kelola destinasi wisata</span>
                </div>
                <i class="fa-solid fa-chevron-right action-arrow"></i>
            </a>

            {{-- Promo / Banner Card --}}
            <a href="#" class="promo-card">
                <div class="promo-text">
                    <div class="promo-label">Destinasi Unggulan</div>
                    <div class="promo-title">TAFKER<br>WISATA</div>
                    <div class="promo-sub">Inspirasi Destinasi: Malang Raya</div>
                </div>
            </a>
        </div>

        {{-- Alert perawatan kendaraan --}}
        @if(($kendaraanPerluPerawatan ?? 0) > 0)
        <div class="alert-card">
            <div class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
                <div class="alert-title">Peringatan Perawatan Kendaraan</div>
                <div class="alert-desc">{{ $kendaraanPerluPerawatan }} kendaraan perlu pengecekan segera.</div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ===== RECENT BOOKINGS ===== --}}
<div class="recent-card">
    <div class="recent-header">
        <h3>Booking Terbaru</h3>
        <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}" class="view-all">Lihat Semua →</a>
    </div>
    <table class="booking-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pelanggan</th>
                <th>Paket</th>
                <th>Status</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $avatarColors = ['a','b','c','d','e']; @endphp
            @forelse($recentBookings ?? [] as $i => $booking)
            <tr>
                <td><span class="booking-id">#BK-{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}</span></td>
                <td>
                    <div class="customer-info">
                        <div class="avatar {{ $avatarColors[$i % 5] }}">
                            {{ strtoupper(substr(optional($booking->pelanggan)->nama ?? 'U', 0, 2)) }}
                        </div>
                        <span class="customer-name">{{ optional($booking->pelanggan)->nama ?? '-' }}</span>
                    </div>
                </td>
                <td>{{ optional($booking->paket)->nama_paket ?? '-' }}</td>
                <td>
                    @php
                        $pill = match($booking->status_booking) {
                            'aktif'   => ['confirmed', 'Aktif'],
                            'pending' => ['pending',   'Menunggu'],
                            'batal'   => ['batal',     'Batal'],
                            'selesai' => ['selesai',   'Selesai'],
                            default   => ['pending',   ucfirst($booking->status_booking)],
                        };
                    @endphp
                    <span class="status-pill {{ $pill[0] }}">{{ $pill[1] }}</span>
                </td>
                <td><span class="amount">Rp {{ number_format($booking->total_biaya ?? 0, 0, ',', '.') }}</span></td>
                <td>
                    <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}?page=detail&id=BK{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}"
                    style="color:#2563eb; font-size:12px; font-weight:600; text-decoration:none;">
                        Lihat →
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; color:#94a3b8; padding:36px 0; font-size:13px;">
                    <i class="fa-regular fa-folder-open" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                    Belum ada data booking.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
const dataBulanan = {
    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
    data: @json($chartMonthly ?? array_fill(0, 12, 0)),
};
const dataHarian = {
    labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
    data: @json($chartDaily ?? array_fill(0, 7, 0)),
};

const ctx = document.getElementById('revenueChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 200);
gradient.addColorStop(0, 'rgba(37,99,235,0.18)');
gradient.addColorStop(1, 'rgba(37,99,235,0.0)');

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dataBulanan.labels,
        datasets: [{
            data: dataBulanan.data,
            borderColor: '#2563eb',
            borderWidth: 2.5,
            backgroundColor: gradient,
            fill: true,
            tension: 0.45,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: '#2563eb',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                titleColor: '#94a3b8',
                bodyColor: '#fff',
                bodyFont: { family: 'Poppins', size: 13 },
                padding: 10,
                cornerRadius: 10,
                callbacks: { label: c => 'Rp ' + c.raw.toLocaleString('id-ID') }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { font: { family:'Poppins', size:10 }, color:'#94a3b8' }
            }, 
            y: {
                min: 0,
                grid: { color:'#f1f5f9', drawBorder: false },
                border: { display: false },
                ticks: {
                    font: { family:'Poppins', size:10 },
                    color: '#94a3b8',
                    callback: v => {
                        if (v === 0) return 'Rp 0';
                        if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(0) + 'jt';
                        if (v >= 1000)    return 'Rp ' + (v/1000).toFixed(0) + 'rb';
                        return 'Rp ' + v;
                    }
                }
            }
        }
    }
});

// Auto buka modal tambah booking jika redirect dari dashboard
if (window.location.hash === '#tambah') {
    // Tunggu halaman data booking load dulu
    window.addEventListener('load', function() {
        if (typeof openTambahBooking === 'function') {
            openTambahBooking();
        }
    });
}

function switchChart(tipe) {
    const d = tipe === 'bulanan' ? dataBulanan : dataHarian;
    chart.data.labels = d.labels;
    chart.data.datasets[0].data = d.data;
    chart.update();
    document.getElementById('btnHarian').classList.toggle('active', tipe === 'harian');
    document.getElementById('btnBulanan').classList.toggle('active', tipe === 'bulanan');
}
</script>
@endpush