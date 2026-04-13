<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTrans Nusa</title>

    <link rel="stylesheet" href="{{ asset('css/user/landing.css') }}">
</head>
<body>

    <!-- HEADER -->
    <header class="navbar">
        <div class="nav-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
        </div>

        <nav class="nav-menu">
            <a href="#landingpage" class="active">Beranda</a>
            <span>|</span>
            <a href="{{ route('guest.katalogpaketwisata') }}">Paket Wisata</a>
            <span>|</span>
            <a href="#tentangkami" >Tentang Kami</a>
            <span>|</span>
            <a href="#kontak">Kontak</a>
        </nav>

    <div class="nav-right">
        <a href="/login" class="btn-login">Masuk</a>
        <a href="/register" class="btn-register">Daftar</a>
    </div>
</header>

<section id="landingpage" class="hero">
        <img src="{{ asset('img/hero-bus.png') }}" alt="Bus Hero" class="hero-bg" width="100%" height="100%">
    </section>


<section id="paketwisata" class="paket-section">
    <h2 class="section-title">Paket Wisata Populer</h2>
    <p class="section-subtitle">Pilihan paket wisata terbaru untuk perjalanan Anda</p>

    <div class="catalog-grid">
        @forelse($paketTerbaru as $paket)
            <div class="catalog-card">
                <img 
                    src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('img/pantai.png') }}" 
                    alt="{{ $paket->nama_paket }}"
                >

                <div class="catalog-body">
                    <h3>{{ $paket->nama_paket }}</h3>
                    <p class="location">📍 {{ $paket->trayek->kotaTujuan->nama_kota ?? '-' }}</p>
                   <p>Sisa Kuota {{ $paket->sisa_kursi }}/{{ $paket->kapasitas }}</p>
                    <p class="label-harga">Harga Mulai Dari</p>

                    <div class="card-footer">
                        <strong>Rp {{ number_format($paket->harga, 0, ',', '.') }}</strong>
                        <a href="{{ route('guest.detailpaket', $paket->id_paket) }}">
    Lihat Detail
</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-box">
                <h3>Belum ada data paket wisata</h3>
            </div>
        @endforelse
    </div>
</section>


<section id="tentangkami" class="alasan-section">
    <h2 class="section-title">Kenapa Memilih Travel Nusantara?</h2>
    <p class="section-subtitle">Kami menyediakan layanan terbaik untuk perjalanan Anda</p>

    <div class="alasan-grid">
        <div class="alasan-card">
            <div class="icon-circle">📍</div>
            <h3>Berbagai Destinasi</h3>
            <p>Jelajahi destinasi wisata terbaik di seluruh Indonesia</p>
        </div>

        <div class="alasan-card">
            <div class="icon-circle">🚌</div>
            <h3>Kendaraan Nyaman</h3>
            <p>Armada pariwisata dengan fasilitas lengkap</p>
        </div>

        <div class="alasan-card">
            <div class="icon-circle">🛡️</div>
            <h3>Aman & Terpercaya</h3>
            <p>Perjalanan aman dengan pelayanan yang terpercaya</p>
        </div>
    </div>
</section>

 <section class="cta-section">
        <h2>Siap Memulai Pertualangan Anda?</h2>
        <p>Booking Sekarang Dan Dapatkan Harga Terbaik Untuk Liburan Impian Anda</p>

        <div class="cta-buttons">
            <a href="{{ route('login') }}" class="btn-cta-white">Booking Sekarang</a>
            <a href="{{ route('login') }}" class="btn-cta-white">Request Wisata</a>
        </div>
    </section>


    <footer id="kontak" class="footer">
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

<script>
document.addEventListener("DOMContentLoaded", function () {

    const sections = document.querySelectorAll("section[id], footer[id]");
    const navLinks = document.querySelectorAll(".nav-menu a");

    window.addEventListener("scroll", () => {
        let scrollY = window.scrollY;

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute("id");

            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {

                navLinks.forEach(link => link.classList.remove("active"));

                const activeLink = document.querySelector(`.nav-menu a[href="#${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add("active");
                }
            }
        });
    });

});
</script>

</body>
</html>