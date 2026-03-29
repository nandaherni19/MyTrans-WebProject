<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Paket Wisata</title>
    <link rel="stylesheet" href="{{ asset('css/user/detail-paket.css') }}">
</head>
<body>

<!-- NAVBAR -->
    <header class="navbar">
        <div class="nav-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
        </div>

        <nav class="nav-menu">
            <a href="#">Beranda</a>
            <span>|</span>
            <a href="#" class="active">Paket Wisata</a>
            <span>|</span>
            <a href="#">Booking</a>
            <span>|</span>
            <a href="#">Riwayat Booking</a>
            <span>|</span>
            <a href="#">Profil</a>
            
        </nav>

        <div class="nav-action">
            <a href="javascript:history.back()" class="btn-kembali">← Kembali</a>
        </div>
    </header>

    <!-- MAIN -->
    <section class="detail-section">
        <div class="detail-container">

            <!-- LEFT CONTENT -->
            <div class="detail-left">
                <img src="{{ asset('img/pantaiwatukarung2.jpg') }}" alt="Pantai Watu Karung" class="detail-image">

                <h1 class="detail-title">Paket Wisata Pantai Watu Karung</h1>
                <div class="rating">
                    <span class="star">⭐</span>
                    <span class="rating-number">4.8</span>
                </div>

                <div class="info-box">
                    <div class="info-item">
                        <div class="info-icon blue">📅</div>
                        <div>
                            <p class="info-label">Durasi</p>
                            <h4>3 Hari 2 Malam</h4>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon green">👤</div>
                        <div>
                            <p class="info-label">Kapasitas</p>
                            <h4>50 Orang</h4>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon purple">📍</div>
                        <div>
                            <p class="info-label">Trayek</p>
                            <h4>Magetan - Pacitan</h4>
                        </div>
                    </div>
                </div>

                <!-- TAB BUTTON -->
                <div class="tab-switcher">
                    <button class="tab-btn active" onclick="showTab('deskripsi')">Deskripsi</button>
                    <button class="tab-btn" onclick="showTab('fasilitas')">Fasilitas</button>
                </div>

                <!-- TAB DESKRIPSI -->
                <div id="deskripsi" class="tab-content active">
                    <div class="content-card">
                        <h3>Tentang Paket Wisata</h3>
                        <p>
                            Pantai Watu Karung merupakan salah satu destinasi selancar terkenal dengan ombak
                            yang dapat mencapai ketinggian hingga sekitar 4 meter, sehingga banyak diminati oleh
                            para peselancar, termasuk peselancar dunia seperti Bruce Irons. Popularitas pantai ini
                            semakin meningkat setelah foto Bruce Irons saat berselancar di Watu Karung menjadi sampul
                            majalah selancar internasional Waves, yang kemudian menarik perhatian peselancar dari berbagai negara.
                        </p>

                        <p>
                            Selain ombaknya yang menantang, Pantai Watu Karung juga menawarkan pemandangan matahari
                            terbenam yang indah di tengah laut karena posisinya yang menghadap ke selatan dan sedikit ke barat.
                            Di sisi timur pantai terdapat sungai bernama Kali Congkel dengan air jernih berwarna kehijauan
                            yang menambah daya tarik keindahan alam di kawasan ini.
                        </p>

                        <h4>Lokasi :</h4>
                        <p>
                            Pringkuku, Ketro, Watukarung, Kecamatan Pacitan, Kabupaten Pacitan,
                            Jawa Timur 63552, Indonesia
                        </p>
                    </div>
                </div>

                <!-- TAB FASILITAS -->
                <div id="fasilitas" class="tab-content">
                    <div class="content-card">
                        <h3>Fasilitas yang didapatkan</h3>
                        <ul class="check-list">
                            <li>Transportasi wisata yang nyaman selama perjalanan</li>
                            <li>Tiket masuk kawasan wisata Pantai Watu Karung</li>
                            <li>Tour guide atau pemandu wisata berpengalaman</li>
                            <li>Makan selama perjalanan wisata</li>
                            <li>Air mineral selama perjalanan</li>
                            <li>Dokumentasi perjalanan wisata</li>
                            <li>Parkir kendaraan di area wisata</li>
                        </ul>

                        <h3>Fasilitas yang tidak didapatkan</h3>
                        <ul class="minus-list">
                            <li>Pengeluaran pribadi selama wisata</li>
                            <li>Sewa papan selancar atau perlengkapan surfing</li>
                            <li>Tiket wahana tambahan di area wisata</li>
                            <li>Tips untuk driver atau tour guide</li>
                            <li>Aktivitas tambahan di luar itinerary perjalanan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- RIGHT CARD -->
            <div class="detail-right">
                <div class="booking-card">
                    <p class="price-label">Harga mulai dari</p>
                    <h2>Rp 1.500.000</h2>
                    <p class="per-person">per orang</p>

                    <a href="#" class="btn-booking">Booking Sekarang</a>

                    <ul class="benefit-list">
                        <li>✅ Konfirmasi Instan</li>
                        <li>✅ Pembatalan Gratis 24 Jam</li>
                        <li>✅ Dijamin Harga Terbaik</li>
                    </ul>

                    <hr>

                    <h3>Butuh Bantuan?</h3>
                    <p class="help-text">Hubungi kami untuk informasi lebih lanjut</p>

                    <a href="#" class="btn-service">Hubungi Customer Service</a>
                </div>
            </div>

        </div>
    </section>

    <script>
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-btn');

            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');

            if (tabId === 'deskripsi') {
                buttons[0].classList.add('active');
            } else {
                buttons[1].classList.add('active');
            }
        }
    </script>

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