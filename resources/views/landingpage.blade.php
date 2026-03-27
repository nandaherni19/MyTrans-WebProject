<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTrans Nusa</title>

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

</head>
<body>

    <header class="navbar">
        <div class="nav-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        </div>

        <nav class="nav-center">
            <a href="#">Beranda</a>
             <span>|</span>
            <a href="#">Paket Wisata</a>
             <span>|</span>
            <a href="#">Tentang Kami</a>
             <span>|</span>
            <a href="#">Kontak</a>
        </nav>

        <div>
            <a href="/login" class="btn-login">Masuk</a>
            <a href="/register" class="btn-register">Daftar</a>
        </div>
    </header>

    <section>
        <img src="{{ asset('images/hero-bus.png') }}" alt="Hero Bus" class="hero-image">
    </section>

   <section class="paket-section">
    <h2 class="section-title">Paket Wisata Populer</h2>
    <p class="section-subtitle">
        Pilihan Paket Wisata Terbaik Dengan Destinasi Menarik Dan Harga Terjangkau
    </p>

    <div class="paket-grid">
        <div class="paket-card">
            <img src="{{ asset('images/pantai.png') }}" alt="Pantai" class="paket-image">

            <div class="paket-content">
                <h3>Pantai Pacitan</h3>
                <p class="lokasi">Pacitan</p>
                <p class="kapasitas">Kapasitas 50 Orang</p>
                <p class="harga-label">Harga Mulai Dari</p>

                <div class="paket-footer">
                    <h4>Rp 2.500.000</h4>
                    <button>Lihat Detail</button>
                </div>
            </div>
        </div>

        <div class="paket-card">
            <img src="{{ asset('images/pantai.png') }}" alt="Pantai" class="paket-image">

            <div class="paket-content">
                <h3>Pantai Pacitan</h3>
                <p class="lokasi">Pacitan</p>
                <p class="kapasitas">Kapasitas 50 Orang</p>
                <p class="harga-label">Harga Mulai Dari</p>

                <div class="paket-footer">
                    <h4>Rp 2.500.000</h4>
                    <button>Lihat Detail</button>
                </div>
            </div>
        </div>

        <div class="paket-card">
            <img src="{{ asset('images/pantai.png') }}" alt="Pantai" class="paket-image">

            <div class="paket-content">
                <h3>Pantai Pacitan</h3>
                <p class="lokasi">Pacitan</p>
                <p class="kapasitas">Kapasitas 50 Orang</p>
                <p class="harga-label">Harga Mulai Dari</p>

                <div class="paket-footer">
                    <h4>Rp 2.500.000</h4>
                    <button>Lihat Detail</button>
                </div>
            </div>
        </div>
    </div>
</section>

   <section class="alasan-section">
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

    <section class="review-section">
    <h2 class="section-title">Review Customer</h2>

    <div class="review-grid">
        <div class="review-card">
            <p class="review-text">
                “Pelayanan dari tim My Travel sangat membantu dan responsif. Perjalanannya nyaman dan destinasi yang dipilih sangat menarik”
            </p>

            <div class="review-stars">★★★★★</div>

            <p class="review-email">Olivia@gmail.com</p>
            <p class="review-location">📍 Pantai Pacitan</p>

            <img src="{{ asset('images/pantai.png') }}" alt="Review" class="review-image">
        </div>

        <div class="review-card">
            <p class="review-text">
                “Pelayanan dari tim My Travel sangat membantu dan responsif. Perjalanannya nyaman dan destinasi yang dipilih sangat menarik”
            </p>

            <div class="review-stars">★★★★★</div>

            <p class="review-email">Olivia@gmail.com</p>
            <p class="review-location">📍 Pantai Pacitan</p>

            <img src="{{ asset('images/pantai.png') }}" alt="Review" class="review-image">
        </div>

        <div class="review-card">
            <p class="review-text">
                “Pelayanan dari tim My Travel sangat membantu dan responsif. Perjalanannya nyaman dan destinasi yang dipilih sangat menarik”
            </p>

            <div class="review-stars">★★★★★</div>

            <p class="review-email">Olivia@gmail.com</p>
            <p class="review-location">📍 Pantai Pacitan</p>

            <img src="{{ asset('images/pantai.png') }}" alt="Review" class="review-image">
        </div>
    </div>
</section>

    <footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <div class="footer-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo MyTrans" class="footer-logo">
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
                <p>📞 085664837559</p>
                <p>📷 @myTranss_</p>
                <p>🎵 @Pariwisataku_</p>
                <p>📍 Alamat Magetan, Jawa Timur, Indonesia</p>
                <p>✉️ Email mytransnusa@gmail.com</p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        © 2026 <strong>MyTransPariwisata</strong>. All rights reserved.
    </div>
</footer>

</body>
</html>