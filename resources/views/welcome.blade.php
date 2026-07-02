@extends('layouts.user')

@section('title', 'MyTrans Pariwisata')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

    <!-- HERO -->
    <section id="beranda" class="hero">
        <img src="{{ asset('img/hero-bus.png') }}" alt="Bus Hero" class="hero-bg">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <div class="hero-badge">Est. 2024 &nbsp;·&nbsp; Magetan, Jawa Timur</div>
            <h1>Jelajahi Indonesia<br>dengan <em>Perjalanan</em><br>yang Berkesan</h1>
            <p>Temukan paket wisata terbaik dengan harga terjangkau, armada nyaman, dan pelayanan yang terpercaya bersama
                MyTrans Pariwisata.</p>
            <div class="hero-actions">
                <a href="{{ route('dashboard.user.katalogpaketwisata') }}" class="btn-hero-primary">
                    🗺️ Lihat Paket Wisata
                </a>
                <a href="/login" class="btn-hero-secondary">Request Perjalanan</a>
            </div>
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-icon">🚌</span>
                <div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Armada Kendaraan</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="stat-icon">📍</span>
                <div>
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Destinasi Wisata</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="stat-icon">😊</span>
                <div>
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
            </div>
            <div class="hero-stat">
                <span class="stat-icon">⭐</span>
                <div>
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Rating Pelanggan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- STORY -->
    <section id="tentang" class="story-section">
        <div>
            <div class="section-label">Tentang Kami</div>
            <h2 class="section-title">Perjalanan Dimulai dari<br>Kepercayaan Anda</h2>
            <p class="section-body">
                MyTrans Pariwisata hadir sejak 2024 dengan misi menyediakan layanan wisata yang nyaman, aman, dan terjangkau
                untuk masyarakat Indonesia.
            </p>
            <p class="section-body">
                Dengan armada yang terawat, driver berpengalaman, dan tim yang berdedikasi, kami berkomitmen memberikan
                pengalaman perjalanan yang tak terlupakan ke berbagai destinasi terbaik di Indonesia.
            </p>

            <div class="story-metrics">
                <div class="metric-card">
                    <div class="metric-number">98%</div>
                    <div class="metric-label">Tingkat Kepuasan Pelanggan</div>
                </div>
                <div class="metric-card">
                    <div class="metric-number">500+</div>
                    <div class="metric-label">Trip Sukses Diselesaikan</div>
                </div>
            </div>
        </div>

        <div class="story-images">
            <img src="{{ asset('img/hero-bus.png') }}" alt="Bus MyTrans" class="story-img" style="object-position: center;">
            <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80" alt="Destinasi"
                class="story-img">
            <img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=400&q=80" alt="Perjalanan"
                class="story-img">
        </div>
    </section>

    <!-- WHY CHOOSE -->
    <section id="kontak" class="why-section">
        <div class="section-label">Keunggulan Kami</div>
        <h2 class="section-title">Kenapa Memilih MyTrans?</h2>
        <p class="why-subtitle">Kami menyediakan layanan terbaik untuk setiap perjalanan Anda</p>

        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon">📍</div>
                <h3>Berbagai Destinasi</h3>
                <p>Jelajahi destinasi wisata terbaik di seluruh Indonesia dengan panduan lokal yang berpengalaman.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">🚌</div>
                <h3>Kendaraan Premium</h3>
                <p>Armada pariwisata modern dengan AC, kursi nyaman, dan fasilitas lengkap untuk kenyamanan perjalanan Anda.
                </p>
            </div>
            <div class="why-card">
                <div class="why-icon">🛡️</div>
                <h3>Aman & Terpercaya</h3>
                <p>Driver profesional berpengalaman dan terlatih, dengan track record keselamatan yang sangat baik.</p>
            </div>
        </div>
    </section>

    <!-- PAKET POPULER -->
    <section id="paketwisata" class="paket-section">
        <div class="paket-header">
            <div>
                <div class="section-label">Pilihan Terbaik</div>
                <h2 class="section-title">Paket Wisata Populer</h2>
            </div>
            <a href="{{ route('guest.katalogpaketwisata') }}" class="btn-lihat-semua">Lihat Semua →</a>
        </div>

        <div class="catalog-grid">
            @forelse($paketTerbaru as $paket)
                <div class="catalog-card">
                    <img src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('img/pantai.png') }}"
                        alt="{{ $paket->nama_paket }}">
                    <div class="catalog-body">
                        <div class="paket-title-row">
                            <h3>{{ $paket->nama_paket }}</h3>
                            <span class="tipe-badge {{ $paket->tipe === 'open_trip' ? 'open-trip' : 'paket-wisata' }}">
                                {{ $paket->tipe === 'open_trip' ? 'Open Trip' : 'Paket Wisata' }}
                            </span>
                        </div>

                        <p class="location">📍 {{ $paket->kota->nama_kota ?? '-' }}@if($paket->kota && $paket->kota->provinsi),
                        {{ $paket->kota->provinsi->nama_provinsi }}@endif
                        </p>

                        @if($paket->kotaLayanan->isNotEmpty())
                            <p class="location">🚐 {{ $paket->kotaLayanan->pluck('nama_kota')->join(', ') }}</p>
                        @endif

                        @if($paket->tipe === 'open_trip')
                            <div class="paket-info-row">
                                <span class="paket-info-label">Berangkat</span>
                                <span
                                    class="paket-info-value">{{ $paket->tanggal_berangkat ? \Carbon\Carbon::parse($paket->tanggal_berangkat)->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="paket-info-row">
                                <span class="paket-info-label">Sisa Kuota</span>
                                <span class="paket-info-value">{{ $paket->sisa_kursi }}/{{ $paket->kapasitas }}</span>
                            </div>
                        @else
                            <div class="paket-info-row">
                                <span class="paket-info-label">Tanggal</span>
                                <span class="paket-info-value">Request</span>
                            </div>
                            <div class="paket-info-row">
                                <span class="paket-info-label">Min. Peserta</span>
                                <span class="paket-info-value">{{ $paket->min_peserta ?? '-' }} orang</span>
                            </div>
                        @endif

                        <div class="paket-info-row">
                            <span class="paket-info-label">Durasi</span>
                            <span class="paket-info-value">{{ $paket->durasi }} hari</span>
                        </div>

                        <div class="card-footer">
                            <strong>Rp {{ number_format($paket->harga, 0, ',', '.') }}</strong>
                            <a href="{{ route('guest.detailpaket', $paket->id_paket) }}">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <h3>Belum ada paket wisata</h3>
                </div>
            @endforelse
        </div>
    </section>

<section class="paket-section">

    <div class="paket-header">
        <div>
            <div class="section-label">Armada Kami</div>
            <h2 class="section-title">Kendaraan Populer</h2>
        </div>

        <a href="{{ route('guest.katalogkendaraan') }}"
        class="btn-lihat-semua">
            Lihat Semua →
        </a>
    </div>

    <div class="kendaraan-grid-home">

        @foreach($kendaraanTerbaru as $kendaraan)

        <div class="kendaraan-card-home">

            <div class="card-image">

                <img
                    src="{{ $kendaraan->foto_kendaraan
                        ? asset('storage/'.$kendaraan->foto_kendaraan)
                        : asset('img/default.png') }}"
                    alt="{{ $kendaraan->nama_kendaraan }}">

                <span class="kendaraan-status-home">
                    {{ ucfirst($kendaraan->status_kendaraan) }}
                </span>

            </div>

            <div class="kendaraan-body-home">

                    <div class="kendaraan-title-row">
                        <h3>{{ $kendaraan->nama_kendaraan }}</h3>
                    </div>

                    <p class="card-type">
                        🚘 {{ ucfirst($kendaraan->jenis_kendaraan) }}
                    </p>

                    <p class="card-desc">
                        Kendaraan nyaman dan siap digunakan untuk perjalanan wisata maupun kebutuhan transportasi lainnya.
                    </p>

                    <p class="cap-row">
                        👥 Kapasitas:
                        <span>{{ $kendaraan->kapasitas }} Orang</span>
                    </p>
                    
                    <div class="kendaraan-divider-home"></div>

                    <div class="kendaraan-price-home">
                         Rp {{ number_format($kendaraan->harga_sewa,0,',','.') }}
                    </div>

                    <span class="kendaraan-unit-home">
                        /hari
                    </span>

                   <div class="kendaraan-action-home">

                        <a href="{{ route('dashboard.user.detailkendaraan', $kendaraan->id_kendaraan) }}"
                            class="btn-detail-home">
                            Lihat Detail
                        </a>

                        <a href="{{ route('dashboard.user.detailkendaraan', $kendaraan->id_kendaraan) }}"
                            class="btn-booking-home">
                            Pesan Sekarang
                        </a>

                    </div>

                </div>

        </div>

        @endforeach

    </div>

</section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>Siap Memulai Petualangan Anda?</h2>
        <p>Booking sekarang dan dapatkan harga terbaik untuk liburan impian Anda bersama MyTrans.</p>
        <div class="cta-buttons">
            @auth
                <a href="{{ route('dashboard.user.katalogpaketwisata') }}" class="btn-cta-primary">🎟️ Booking Sekarang</a>
                <a href="{{ route('dashboard.user.requestbooking') }}" class="btn-cta-outline">Request Wisata</a>
            @else         
                <a href="{{ route('login') }}" class="btn-cta-primary">🎟️ Booking Sekarang</a>
                <a href="{{ route('login') }}" class="btn-cta-outline">Request Wisata</a>
            @endauth
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <div class="footer-brand">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo MyTrans">
                    <h3>MY Trans Pariwisata</h3>
                </div>

                <p class="footer-description">
                    MyTransPariwisata menyediakan layanan paket wisata dan sewa kendaraan
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
                        <p>📞 +6282140360481</p>
                        <p>📷 @myTranss_</p>
                        <p>🎵 @Pariwisataku_</p>
                    </div>

                    <div class="contact-col">
                        <p>📍 Alamat Magetan, Jawa Timur, Indonesia</p>
                        <p>📧 Email mytransstravell@gmail.com</p>
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
        const sections = document.querySelectorAll("section[id]");
        const navLinks = document.querySelectorAll(".nav-menu .nav-link");

        window.addEventListener("scroll", () => {

            let current = "";

            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                const sectionHeight = section.offsetHeight;

                if (window.scrollY >= sectionTop &&
                    window.scrollY < sectionTop + sectionHeight) {

                    current = section.getAttribute("id");
                }
            });

            navLinks.forEach(link => {
                link.classList.remove("nav-active");

                if (link.getAttribute("href") === `#${current}`) {
                    link.classList.add("nav-active");
                }
            });

        });
    </script>
@endpush