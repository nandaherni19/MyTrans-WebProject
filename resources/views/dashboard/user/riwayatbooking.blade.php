@extends('layouts.user')

@section('title', 'Riwayat Booking')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/riwayatbooking.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<main class="container">

    @if($page !== 'detail')
        <h1 class="page-title">Riwayat Booking</h1>
        <p class="page-subtitle">Lihat semua riwayat booking anda</p>

        <div class="booking-list">
            @forelse($riwayat as $item)
                @php
                    $status = strtolower($item['status'] ?? 'pending');

                    switch ($status) {
                        case 'pending':
                        case 'menunggu':
                            $statusClass = 'status-orange';
                            $statusLabel = 'Menunggu';
                            break;

                        case 'disetujui':
                            $statusClass = 'status-blue';
                            $statusLabel = 'Disetujui';
                            break;

                        case 'confirmed':
                        case 'dikonfirmasi':
                            $statusClass = 'status-blue';
                            $statusLabel = 'Dikonfirmasi';
                            break;

                        case 'dp_lunas':
                            $statusClass = 'status-blue';
                            $statusLabel = 'DP Lunas';
                            break;

                        case 'lunas':
                        case 'selesai':
                        case 'done':
                            $statusClass = 'status-green';
                            $statusLabel = 'Lunas';
                            break;

                        case 'dibatalkan':
                        case 'cancelled':
                        case 'batal':
                            $statusClass = 'status-red';
                            $statusLabel = 'Dibatalkan';
                            break;

                        default:
                            $statusClass = 'status-blue';
                            $statusLabel = ucfirst($item['status'] ?? '-');
                            break;
                    }
                @endphp

                <div class="booking-card">
                    <div class="booking-card-top">
                        <div class="booking-info">
                            <h3>{{ $item['judul'] ?? '-' }}</h3>
                            <p>Booking ID: {{ $item['booking_id'] ?? '-' }}</p>
                        </div>

                        <div class="booking-badge-group">
                            @if(!empty($item['detail_url']))
                                <a href="{{ $item['detail_url'] }}" class="detail-btn detail-outline">
                                    Detail Pesanan
                                </a>
                            @endif

                            <span class="detail-btn detail-primary payment-label">
                                {{ $item['payment_label'] }}
                            </span>

                            <div class="status-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </div>
                        </div>
                    </div>

                    <div class="booking-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $item['lokasi'] ?? '-' }}</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>
                                {{ !empty($item['tanggal']) ? \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('l, j F Y') : '-' }}
                            </span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-users"></i>
                            <span>{{ $item['jumlah_peserta'] ?? 0 }} orang</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    Belum ada riwayat booking.
                </div>
            @endforelse
        </div>
    @endif

    @if($page === 'detail')
        <h1 class="page-title detail-page-title">Detail Pesanan</h1>
        <p class="page-subtitle detail-page-subtitle">Lihat detail pesanan anda</p>

        <section class="detail-card">
            <div class="detail-top">
                <div class="detail-image">
                    <img
                        src="{{ !empty($detail['gambar']) ? asset($detail['gambar']) : asset('img/jatimPark2.jpg') }}"
                        alt="Detail Booking"
                    >
                </div>

                <div class="detail-info-box">
                    <h2 class="detail-package-title">{{ $detail['judul'] ?? '-' }}</h2>

                    <div class="detail-info-list">
                        <div class="detail-row status-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-separator">:</span>
                            <span class="detail-value">{{ $detail['status'] ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Booking ID</span>
                            <span class="detail-separator">:</span>
                            <span class="detail-value">{{ $detail['booking_id'] ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Tanggal Berangkat</span>
                            <span class="detail-separator">:</span>
                            <span class="detail-value">{{ $detail['tanggal_berangkat'] ?? '-' }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Jumlah Penumpang</span>
                            <span class="detail-separator">:</span>
                            <span class="detail-value">{{ $detail['jumlah_peserta'] ?? '-' }} Orang</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-separator">:</span>
                            <span class="detail-value">{{ $detail['lokasi'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-divider"></div>

            <div class="detail-bottom">
                <div class="trayek-box">
                    <h3 class="detail-section-title">
                        <i class="fa-solid fa-location-dot"></i>
                        Trayek Perjalanan
                    </h3>

                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Titik Awal</span>
                                <span class="timeline-line"></span>
                                <strong>{{ $detail['titik_awal'] ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Tujuan</span>
                                <span class="timeline-line"></span>
                                <strong>{{ $detail['tujuan'] ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="desc-box">
                    <h3 class="detail-section-title">Deskripsi</h3>
                    <div class="desc-content">
                        {{ $detail['deskripsi'] ?? '-' }}
                    </div>
                </div>
            </div>
        </section>
    @endif

</main>
@endsection

@push('scripts')
@endpush