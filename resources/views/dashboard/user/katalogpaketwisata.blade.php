@extends('layouts.user')

@section('title', 'Katalog Paket Wisata')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/kpw.css') }}">
@endpush

@section('content')
    <section class="hero-title">
        <h1>Katalog Paket Wisata</h1>
        <p>Temukan Paket Wisata Impian Anda!</p>
    </section>

    <section class="search-section">
        <div class="search-box">
            <input type="text" id="searchPaket" placeholder="Cari paket wisata...">
        </div>

        @auth
            <a href="{{ route('dashboard.user.requestbooking') }}" class="btn-request">
                Request Booking
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-request">
                Request Booking
            </a>
        @endauth
    </section>

    <section class="catalog-section">
        <div class="catalog-grid">

            @forelse($pakets as $paket)
                <div class="catalog-card paket-card" data-nama="{{ strtolower($paket->nama_paket) }}"
                    data-lokasi="{{ strtolower($paket->kota->nama_kota ?? '-') }}">

                    <img src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('img/pantai.png') }}"
                        alt="{{ $paket->nama_paket }}">

                    <div class="catalog-body">
                        <div class="paket-title-row">
                            <h3>{{ $paket->nama_paket }}</h3>

                            <span class="tipe-badge 
                                            {{ $paket->tipe === 'open_trip' ? 'open-trip' : 'paket-wisata' }}">
                                {{ $paket->tipe === 'open_trip' ? 'Open Trip' : 'Paket Wisata' }}
                            </span>
                        </div>

                        <p class="location">
                            📍 {{ $paket->kota->nama_kota ?? '-' }}
                            @if($paket->kota && $paket->kota->provinsi)
                                , {{ $paket->kota->provinsi->nama_provinsi }}
                            @endif
                        </p>

                        {{-- TAMBAHKAN INI --}}
                        @if($paket->kotaLayanan->isNotEmpty())
                            <p class="location">
                                🚐 Kota Dilayani: {{ $paket->kotaLayanan->pluck('nama_kota')->join(', ') }}
                            </p>
                        @endif

                        @if($paket->tipe === 'open_trip')
                            <div class="paket-info-row">
                                <span class="paket-info-label">Tanggal Berangkat</span>
                                <span class="paket-info-value">
                                    {{ $paket->tanggal_berangkat ? \Carbon\Carbon::parse($paket->tanggal_berangkat)->format('d.m.Y') : '-' }}
                                </span>
                            </div>

                            <div class="paket-info-row">
                                <span class="paket-info-label">Tanggal Kembali</span>
                                <span class="paket-info-value">
                                    {{ $paket->tanggal_kembali ? \Carbon\Carbon::parse($paket->tanggal_kembali)->format('d.m.Y') : '-' }}
                                </span>
                            </div>

                            <div class="paket-info-row">
                                <span class="paket-info-label">Sisa Kuota</span>
                                <span class="paket-info-value">
                                    {{ $paket->sisa_kursi }}/{{ $paket->kapasitas }}
                                </span>
                            </div>
                        @else
                            <div class="paket-info-row">
                                <span class="paket-info-label">Tanggal</span>
                                <span class="paket-info-value">Request</span>
                            </div>

                            <div class="paket-info-row">
                                <span class="paket-info-label">Minimal Peserta</span>
                                <span class="paket-info-value">
                                    {{ $paket->min_peserta ?? '-' }} orang
                                </span>
                            </div>
                        @endif

                        <div class="paket-info-row">
                            <span class="paket-info-label">Durasi</span>
                            <span class="paket-info-value">
                                {{ $paket->durasi }} hari
                            </span>
                        </div>

                        <div class="card-footer">
                            <strong>Rp {{ number_format($paket->harga, 0, ',', '.') }}</strong>

                            @auth
                                <a href="{{ route('dashboard.user.detailpaket', $paket->id_paket) }}">
                                    Lihat Detail
                                </a>
                            @else
                                <a href="{{ route('guest.detailpaket', $paket->id_paket) }}">
                                    Lihat Detail
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <h3>Belum ada data paket wisata</h3>
                </div>
            @endforelse

        </div>

        <p id="emptySearchMessage" style="display: none; text-align: center; margin-top: 20px;">
            Paket wisata tidak ditemukan.
        </p>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchPaket');
            const cards = document.querySelectorAll('.paket-card');
            const emptyMessage = document.getElementById('emptySearchMessage');

            searchInput.addEventListener('input', function () {
                const keyword = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                cards.forEach(card => {
                    const nama = card.dataset.nama || '';
                    const lokasi = card.dataset.lokasi || '';

                    if (nama.includes(keyword) || lokasi.includes(keyword)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });
    </script>
@endpush