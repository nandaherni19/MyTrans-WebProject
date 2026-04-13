@extends('layouts.user')

@section('title', 'Beranda')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/beranda.css') }}">
@endpush

@section('content')
    <!-- HERO -->
    <section class="hero">
        <img src="{{ asset('img/hero-bus.png') }}" alt="Bus Hero" class="hero-bg">

        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1>Jelajahi Indonesia Bersama Kami</h1>
            <p>
                Temukan paket wisata terbaik dengan harga terjangkau dan
                fasilitas lengkap
            </p>
        </div>
    </section>

    <!-- FITUR -->
    <section class="fitur-section">
        <div class="fitur-card">
            <div class="fitur-icon">📍</div>
            <h3>Berbagai Destinasi</h3>
            <p>Jelajahi destinasi wisata terbaik di seluruh Indonesia</p>
        </div>

        <div class="fitur-card">
            <div class="fitur-icon">🚌</div>
            <h3>Kendaraan Nyaman</h3>
            <p>Armada pariwisata dengan fasilitas lengkap</p>
        </div>

        <div class="fitur-card">
            <div class="fitur-icon">🔒</div>
            <h3>Aman & Terpercaya</h3>
            <p>Perjalanan aman dengan pelayanan yang terpercaya</p>
        </div>
    </section>

    <!-- PAKET -->
    <section class="paket-section">
        <h2>Paket Wisata Populer</h2>
        <p class="paket-subtitle">
            Pilihan Paket Wisata Terpopuler Dengan Destinasi Menarik Dan Harga Terjangkau
        </p>

        <div class="catalog-grid">
    @forelse($pakets as $paket)
        <div class="catalog-card">
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
                    <a href="{{ route('dashboard.user.detailpaket', $paket->id_paket) }}">Lihat Detail</a>
                </div>
            </div>
        </div>
    @empty
        <p>Belum ada paket wisata.</p>
    @endforelse
</div>
       

  
    </section>

    <!-- CTA -->
     <section class="cta-section">
        <h2>Siap Memulai Pertualangan Anda?</h2>
        <p>Booking Sekarang Dan Dapatkan Harga Terbaik Untuk Liburan Impian Anda</p>

        <div class="cta-buttons">
            <a href="{{ route('dashboard.user.booking') }}" class="btn-cta-white">Booking Sekarang</a>
        <a href="{{ route('dashboard.user.requestbooking') }}" class="btn-cta-white">Request Wisata</a>
        </div>
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
    console.log('Halaman beranda loaded');
</script>
@endpush

