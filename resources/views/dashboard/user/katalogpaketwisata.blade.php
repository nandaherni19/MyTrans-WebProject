<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Paket Wisata</title>
    <link rel="stylesheet" href="{{ asset('css/user/kpw.css') }}">
</head>
<body>

   <!-- NAVBAR -->
    <header class="navbar">
        <div class="nav-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
        </div>

        <nav class="nav-menu">
            <a href="{{ route('dashboard.user') }}">Beranda</a>
            <span>|</span>
            <a href="{{ route('dashboard.user.katalogpaketwisata') }}" class="active">Paket Wisata</a>
            <span>|</span>
            <a href="#">Booking</a>
            <span>|</span>
            <a href="#">Riwayat Booking</a>
            <span>|</span>
            <a href="#">Profil</a>
            
        </nav>

        <div class="nav-action">
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout" style="cursor: pointer;">Keluar</button>
            </form>
        </div>
    </header>


    <!-- HERO TITLE -->
    <section class="hero-title">
        <h1>Katalog Paket Wisata</h1>
        <p>Temukan Paket Wisata Impian Anda!</p>
    </section>

    <!-- SEARCH -->
    <section class="search-section">
        <div class="search-box">
            <input type="text" placeholder="Cari paket wisata...">
        </div>

        <a href="#" class="btn-request">request booking</a>
    </section>

    <!-- CARD LIST -->
    <section class="catalog-section">
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

            @for ($i = 0; $i < 5; $i++)
                <div class="catalog-card">
                    <img src="{{ asset('img/pantai.png') }}" alt="Pantai Pacitan">
                    <div class="catalog-body">
                        <h3>Pantai Pacitan</h3>
                        <p class="location">📍 Pacitan</p>
                        <p>Kapasitas 50 Orang</p>
                        <p class="label-harga">Harga Mulai Dari</p>
                        <div class="card-footer">
                            <strong>Rp 2.500.000</strong>
                            <a href="{{ route('dashboard.user.detailpaket') }}">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endfor
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