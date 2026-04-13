@extends('layouts.user')

@section('title', 'Katalog Paket Wisata')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/kpw.css') }}">
@endpush

@section('content')
    <!-- HERO TITLE -->
    <section class="hero-title">
        <h1>Katalog Paket Wisata</h1>
        <p>Temukan Paket Wisata Impian Anda!</p>
    </section>

    <!-- SEARCH -->
    <section class="search-section">
        <div class="search-box">
            <input type="text" id="searchPaket" placeholder="Cari paket wisata...">

        </div>

        <a href="{{ route('dashboard.user.requestbooking') }}" class="btn-request">Request Booking</a>
    </section>

    <!-- CARD LIST -->
  <section class="catalog-section">
    <div class="catalog-grid">

        @forelse($pakets as $paket)
            <div class="catalog-card paket-card"
                data-nama="{{ strtolower($paket->nama_paket) }}"
                data-lokasi="{{ strtolower($paket->trayek->kotaTujuan->nama_kota ?? '-') }}">
                <img 
                    src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('img/pantai.png') }}" 
                    alt="{{ $paket->nama_paket }}"
                >

                <div class="catalog-body">
                    <h3>{{ $paket->nama_paket }}</h3>
  
                    <p class="location">📍 {{ $paket->trayek->kotaTujuan->nama_kota ?? '-' }}
                            </p>

                    <p>Sisa Kuota {{ $paket->sisa_kursi }}/{{ $paket->kapasitas }}</p>
                    <p class="label-harga">Harga Mulai Dari</p>

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

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <div class="footer-brand">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
                    <h3>MY Trans Nusa Pariwisata</h3>
                </div>

                <p class="footer-description">
                    MyTransNusaPariwisata menyediakan layanan paket wisata dan sewa kendaraan
                    untuk membantu Anda menjelajahi berbagai destinasi dengan nyaman dan aman.
                    Dengan armada yang nyaman dan driver berpengalaman, kami berkomitmen memberikan
                    perjalanan yang menyenangkan dan berkesan.
                </p>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-right">
                <h3>Hubungi Kami</h3>

                <div class="footer-contact-list">
                    <div class="contact-col">
                        <p>📞 085664837559</p>
                        <p>📷 @myTranss_</p>
                        <p>🎵 @Pariwisataku_</p>
                    </div>

                    <div class="contact-col">
                        <p>📍 Alamat Magetan, Jawa Timur, Indonesia</p>
                        <p>📧 Email mytransnusa@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            © 2026 <strong>MyTransPariwisata</strong>. All rights reserved.
        </div>
    </footer>
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

        if (visibleCount === 0) {
            emptyMessage.style.display = 'block';
        } else {
            emptyMessage.style.display = 'none';
        }
    });
});
</script>
@endpush

