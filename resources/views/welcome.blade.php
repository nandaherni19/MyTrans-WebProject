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
            <a href="#paketwisata" >Paket Wisata</a>
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

<section class="hero">
        <img src="{{ asset('img/hero-bus.png') }}" alt="Bus Hero" class="hero-bg" width="100%" height="100%">
    </section>

    <section class="paket-section">
    <h2 class="section-title">Paket Wisata Populer</h2>
    <p class="section-subtitle">
        Pilihan Paket Wisata Terbaik Dengan Destinasi Menarik Dan Harga Terjangkau
    </p>

    <div class="catalog-grid">
        
        <div class="catalog-card">
                <img src="{{ asset('img/pantai.png') }}" alt="Pantai Watu Karung">
                <div class="catalog-body">
                    <h3>Pantai Watu Karung</h3>
                    <p class="location">📍 Pacitan</p>
                    <p>Kapasitas 50 Orang</p>
                    <p class="label-harga">Harga Mulai Dari</p>
                    <div class="card-footer">
                        <strong>Rp 1.500.000</strong>
                        <a href="{{ route('dashboard.user.detailpaket') }}">Lihat Detail</a>
                    </div>
                </div>
            </div>

            <div class="catalog-card">
                <img src="{{ asset('img/pantai.png') }}" alt="Pantai Watu Karung">
                <div class="catalog-body">
                    <h3>Pantai Watu Karung</h3>
                    <p class="location">📍 Pacitan</p>
                    <p>Kapasitas 50 Orang</p>
                    <p class="label-harga">Harga Mulai Dari</p>
                    <div class="card-footer">
                        <strong>Rp 1.500.000</strong>
                        <a href="{{ route('dashboard.user.detailpaket') }}">Lihat Detail</a>
                    </div>
                </div>
            </div>

            <div class="catalog-card">
                <img src="{{ asset('img/pantai.png') }}" alt="Pantai Watu Karung">
                <div class="catalog-body">
                    <h3>Pantai Watu Karung</h3>
                    <p class="location">📍 Pacitan</p>
                    <p>Kapasitas 50 Orang</p>
                    <p class="label-harga">Harga Mulai Dari</p>
                    <div class="card-footer">
                        <strong>Rp 1.500.000</strong>
                        <a href="{{ route('dashboard.user.detailpaket') }}">Lihat Detail</a>
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

            <img src="{{ asset('img/pantai.png') }}" alt="Review" class="review-image">
        </div>

        <div class="review-card">
            <p class="review-text">
                “Pelayanan dari tim My Travel sangat membantu dan responsif. Perjalanannya nyaman dan destinasi yang dipilih sangat menarik”
            </p>

            <div class="review-stars">★★★★★</div>

            <p class="review-email">Olivia@gmail.com</p>
            <p class="review-location">📍 Pantai Pacitan</p>

            <img src="{{ asset('img/pantai.png') }}" alt="Review" class="review-image">
        </div>

        <div class="review-card">
            <p class="review-text">
                “Pelayanan dari tim My Travel sangat membantu dan responsif. Perjalanannya nyaman dan destinasi yang dipilih sangat menarik”
            </p>

            <div class="review-stars">★★★★★</div>

            <p class="review-email">Olivia@gmail.com</p>
            <p class="review-location">📍 Pantai Pacitan</p>

            <img src="{{ asset('img/pantai.png') }}" alt="Review" class="review-image">
        </div>
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

</body>
</html>