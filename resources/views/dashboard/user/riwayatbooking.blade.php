@extends('layouts.user')

@section('title', 'Riwayat Booking')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/riwayatbooking.css') }}">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')

    <main class="container">

        {{-- HALAMAN RIWAYAT BOOKING --}}
        @if($page != 'detail')
            <h1 class="page-title">Riwayat Booking</h1>
            <p class="page-subtitle">Lihat semua riwayat booking anda</p>

            <div class="filter-box">
                <div class="filter-tab {{ $filter == 'semua' ? 'active' : '' }}">Semua</div>
                <div class="filter-tab {{ $filter == 'dikonfirmasi' ? 'active' : '' }}">Dikonfirmasi</div>
                <div class="filter-tab {{ $filter == 'menunggu' ? 'active' : '' }}">Menunggu</div>
                <div class="filter-tab {{ $filter == 'selesai' ? 'active' : '' }}">Selesai</div>
            </div>

            <div class="booking-list">

                @if($filter == 'semua' || $filter == 'dikonfirmasi')
                <div class="booking-card">
                    <div class="booking-card-top">
                        <div class="booking-info">
                            <h3>Paket Wisata Pantai Watu Karung</h3>
                            <p>Booking ID: BK001</p>
                        </div>

                        <div class="booking-badge-group">
                            <a href="{{ route('dashboard.user.detailpesanan') }}" class="detail-btn">
    Detail Pesanan
</a>
                            <div class="status-badge status-blue">Dikonfirmasi</div>
                        </div>
                    </div>

                    <div class="booking-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Pacitan, Jawa Timur</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Selasa, 17 Maret 2026</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-users"></i>
                            <span>2 orang</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($filter == 'semua' || $filter == 'menunggu')
                <div class="booking-card">
                    <div class="booking-card-top">
                        <div class="booking-info">
                            <h3>Paket Wisata Gunung Bromo</h3>
                            <p>Booking ID: BK002</p>
                        </div>

                        <div class="booking-badge-group">
                            <div class="detail-btn">Detail Pesanan</div>
                            <div class="status-badge status-orange">Menunggu</div>
                        </div>
                    </div>

                    <div class="booking-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Malang, Jawa Timur</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Selasa, 31 Maret 2026</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-users"></i>
                            <span>20 orang</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($filter == 'semua' || $filter == 'selesai')
                <div class="booking-card">
                    <div class="booking-card-top">
                        <div class="booking-info">
                            <h3>Paket Wisata Jatim Park II</h3>
                            <p>Booking ID: BK003</p>
                        </div>

                        <div class="booking-badge-group">
                            <div class="detail-btn">Detail Pesanan</div>
                            <div class="status-badge status-green">Selesai</div>
                        </div>
                    </div>

                    <div class="booking-meta">
                        <div class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Malang, Jawa Timur</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Rabu, 1 April 2026</span>
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-users"></i>
                            <span>10 orang</span>
                        </div>
                    </div>
                </div>
                @endif

                @if(!in_array($filter, ['semua', 'dikonfirmasi', 'menunggu', 'selesai']))
                <div class="empty-state">
                    Filter tidak ditemukan.
                </div>
                @endif

            </div>
        @endif


        {{-- HALAMAN DETAIL TRAYEK --}}
        @if($page == 'detail')
    <h1 class="page-title detail-page-title">Detail Trayek</h1>
    <p class="page-subtitle detail-page-subtitle">Lihat detail trayek perjalanan anda</p>

    <section class="detail-card">
        <div class="detail-top">
            <div class="detail-image">
                <img src="{{ asset('img/jatimPark2.jpg') }}" alt="Jatim Park II">
            </div>

            <div class="detail-info-box">
                <h2 class="detail-package-title">Paket Wisata Jatim Park II</h2>

                <div class="detail-info-list">
                    <div class="detail-row status-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-separator">:</span>
                        <span class="status-badge status-green detail-status-badge">Selesai</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Booking ID</span>
                        <span class="detail-separator">:</span>
                        <span class="detail-value">BK003</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Tanggal Berangkat</span>
                        <span class="detail-separator">:</span>
                        <span class="detail-value">Rabu, 1 April 2026</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Jumlah Penumpang</span>
                        <span class="detail-separator">:</span>
                        <span class="detail-value">10 Orang</span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Lokasi</span>
                        <span class="detail-separator">:</span>
                        <span class="detail-value">Malang, Jawa Timur</span>
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
                            <strong>Magetan</strong>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-label">Tujuan</span>
                            <span class="timeline-line"></span>
                            <strong>Malang</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="desc-box">
                <h3 class="detail-section-title">Deskripsi</h3>
                <div class="desc-content">
                    Perjalanan wisata kota dingin ke Jatim Park 2 di Kota Batu yang menawarkan pengalaman seru dan edukatif dengan berbagai wahana menarik seperti Museum Satwa dan Batu Secret Zoo.
                </div>
            </div>
        </div>
    </section>
@endif

    </main>

@endsection

@push('scripts')

@endpush

