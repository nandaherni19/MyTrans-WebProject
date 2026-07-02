@extends('layouts.user')

@section('title', 'Detail Paket Wisata')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/detail-paket.css') }}">
@endpush

@section('content')
    <section class="detail-section">
        <div class="detail-container">

            <div class="detail-left">
                <img src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('img/pantai.png') }}"
                    alt="{{ $paket->nama_paket }}" class="detail-image">

                <h1 class="detail-title">{{ $paket->nama_paket }}</h1>

                <div class="rating">
                    <span class="star">⭐</span>
                    <span class="rating-number">4.8</span>
                </div>

                <div class="info-box">
                    <div class="info-item">
                        <div class="info-icon blue">📅</div>
                        <div>
                            <p class="info-label">Durasi</p>
                            <h4>{{ $paket->durasi }} hari</h4>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon green">👤</div>
                        <div>
                            @if($paket->tipe === 'open_trip')
                                <p class="info-label">Kapasitas</p>
                                <h4>{{ $paket->kapasitas }} orang</h4>
                            @else
                                <p class="info-label">Minimal Peserta</p>
                                <h4>{{ $paket->min_peserta }} orang</h4>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon purple">📍</div>
                        <div>
                            <p class="info-label">Lokasi</p>
                            <h4>
                                {{ $paket->kota->nama_kota ?? '-' }}
                                @if($paket->kota && $paket->kota->provinsi)
                                    , {{ $paket->kota->provinsi->nama_provinsi }}
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="tab-switcher">
                    <button class="tab-btn active" onclick="showTab('deskripsi', this)">Deskripsi</button>
                    <button class="tab-btn" onclick="showTab('fasilitas', this)">Fasilitas</button>
                    <button class="tab-btn" onclick="showTab('lokasi', this)">Lokasi</button>
                </div>

                <div id="deskripsi" class="tab-content active">
                    <div class="content-card">
                        <h3>Tentang Paket Wisata</h3>
                        <p>{{ $paket->deskripsi ?? '-' }}</p>
                    </div>
                </div>

                <div id="fasilitas" class="tab-content">
                    <div class="content-card">
                        <h3>Fasilitas yang didapatkan</h3>

                        @php
                            $fasilitas = preg_split('/\r\n|\r|\n/', $paket->fasilitas ?? '');
                            $fasilitas = array_filter($fasilitas);
                        @endphp

                        @if(count($fasilitas) > 0)
                            <ul class="check-list">
                                @foreach($fasilitas as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>-</p>
                        @endif
                    </div>
                </div>

                <div id="lokasi" class="tab-content">
                    <div class="content-card">
                        <h3>Lokasi Tujuan</h3>
                        <p>
                            {{ $paket->kota->nama_kota ?? '-' }}
                            @if($paket->kota && $paket->kota->provinsi)
                                , {{ $paket->kota->provinsi->nama_provinsi }}
                            @endif
                        </p>

                        @if($paket->kotaLayanan->isNotEmpty())
                            <h4>Kota yang Dilayani</h4>
                            <p>{{ $paket->kotaLayanan->pluck('nama_kota')->join(', ') }}</p>
                        @endif

                        @if($paket->tipe === 'open_trip')
                            <h4>Jadwal Open Trip</h4>
                            <p>
                                Berangkat:
                                {{ $paket->tanggal_berangkat ? \Carbon\Carbon::parse($paket->tanggal_berangkat)->format('d M Y') : '-' }}
                            </p>
                            <p>
                                Kembali:
                                {{ $paket->tanggal_kembali ? \Carbon\Carbon::parse($paket->tanggal_kembali)->format('d M Y') : '-' }}
                            </p>
                            <p>
                                Sisa Kuota: {{ $paket->sisa_kursi }}/{{ $paket->kapasitas }}
                            </p>
                        @else
                            <h4>Paket Wisata Custom</h4>
                            <p>Tanggal perjalanan dapat direquest saat booking.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-right">
                <div class="booking-card">
                    <p class="price-label">Harga mulai dari</p>
                    <h2>Rp {{ number_format($paket->harga, 0, ',', '.') }}</h2>

                    <p class="per-person">
                        @if($paket->tipe === 'open_trip')
                            per orang, sudah termasuk kendaraan
                        @else
                            per orang, belum termasuk kendaraan
                        @endif
                    </p>

                    <a href="{{ route('dashboard.user.booking.paket', $paket->id_paket) }}" class="btn-booking">
                        Booking Sekarang
                    </a>

                    <ul class="benefit-list">
                        <li>✅ Konfirmasi Instan</li>
                        <li>✅ Admin siap membantu</li>
                        <li>✅ Dijamin Harga Terbaik</li>
                    </ul>

                    <hr>

                    <h3>Butuh Bantuan?</h3>
                    <p class="help-text">Hubungi kami untuk informasi lebih lanjut</p>

                    @php
                        $lokasi = $paket->kota->nama_kota ?? '-';
                        $provinsi = $paket->kota && $paket->kota->provinsi ? $paket->kota->provinsi->nama_provinsi : '-';
                        $tipe = $paket->tipe === 'open_trip' ? 'Open Trip' : 'Paket Wisata';

                        $pesanWa = "Halo Admin MyTrans Nusa Pariwisata,%0A%0A"
                            . "Saya tertarik dengan paket wisata berikut:%0A"
                            . "Nama Paket: " . $paket->nama_paket . "%0A"
                            . "Tipe: " . $tipe . "%0A"
                            . "Lokasi: " . $lokasi . ", " . $provinsi . "%0A"
                            . "Harga: Rp " . number_format($paket->harga, 0, ',', '.') . "%0A%0A"
                            . "Apakah paket ini masih tersedia? Mohon info lengkapnya ya kak. Terima kasih.";
                    @endphp

                    <a href="https://wa.me/6282140360481?text={{ $pesanWa }}" target="_blank" class="btn-service">
                        Hubungi Customer Service
                    </a>
                </div>
            </div>

        </div>
    </section>

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
                        <p>📞 +6282140360481</p>
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
        function showTab(tabId, element) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-btn');

            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            element.classList.add('active');
        }
    </script>
@endpush