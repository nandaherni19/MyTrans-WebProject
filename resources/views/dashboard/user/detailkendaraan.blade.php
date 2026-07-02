@extends('layouts.user')

@section('title', 'Detail Kendaraan - ' . $kendaraan->nama_kendaraan)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/detail-kendaraan.css') }}">
@endpush

@section('content')

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard.user') }}">Beranda</a>
    <span>›</span>
    <a href="{{ route('dashboard.user.katalogkendaraan') }}">Katalog Kendaraan</a>
    <span>›</span>
    {{ $kendaraan->nama_kendaraan }}
</div>

<section class="detail-section">
    <div class="detail-container">

        {{-- ========================
             KOLOM KIRI
        ======================== --}}
        <div class="detail-left">

            {{-- GALLERY GRID --}}
            <div class="gallery-grid">

                {{-- Foto utama --}}
                <div class="gallery-main">
                    <img
                        src="{{ $kendaraan->foto_kendaraan
                            ? asset('storage/' . $kendaraan->foto_kendaraan)
                            : asset('img/default.png') }}"
                        alt="{{ $kendaraan->nama_kendaraan }}">
                </div>

                {{-- Sub foto (jika ada relasi galeri, ganti dengan loop) --}}
                <div class="gallery-sub">
                    @if(isset($galeri) && $galeri->count() > 0)

                        @foreach($galeri->take(2) as $index => $foto)
                            <div class="gallery-thumb {{ $index === 1 && $galeri->count() > 2 ? 'gallery-thumb-last' : '' }}">
                                <img src="{{ asset('storage/' . $foto->path) }}" alt="Foto {{ $index + 2 }}">
                                @if($index === 1 && $galeri->count() > 2)
                                    <div class="gallery-overlay">
                                        <span>+{{ $galeri->count() - 2 }} Foto</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    @else
                        {{-- Fallback: tampilkan foto utama 2x --}}
                        <div class="gallery-thumb">
                            <img
                                src="{{ $kendaraan->foto_kendaraan
                                    ? asset('storage/' . $kendaraan->foto_kendaraan)
                                    : asset('img/default.png') }}"
                                alt="{{ $kendaraan->nama_kendaraan }}">
                        </div>
                        <div class="gallery-thumb">
                            <img
                                src="{{ $kendaraan->foto_kendaraan
                                    ? asset('storage/' . $kendaraan->foto_kendaraan)
                                    : asset('img/default.png') }}"
                                alt="{{ $kendaraan->nama_kendaraan }}">
                        </div>
                    @endif
                </div>

            </div>

            {{-- BADGE + JUDUL --}}
            <div class="vehicle-badges">
                @if($kendaraan->label ?? false)
                    <span class="badge-class">{{ $kendaraan->label }}</span>
                @else
                    <span class="badge-class">Premium Class</span>
                @endif

                <div class="badge-rating">
                    <span class="star">★</span>
                    <strong>{{ number_format($kendaraan->rating ?? 4.9, 1) }}</strong>
                    <span>/5</span>
                    @if(isset($totalUlasan))
                        <a href="#ulasan">({{ $totalUlasan }} Ulasan)</a>
                    @endif
                </div>
            </div>

            <h1 class="detail-title">{{ $kendaraan->nama_kendaraan }}</h1>

            <p class="vehicle-meta">
                <span>Tipe {{ $kendaraan->jenis_kendaraan }}</span>
                @if($kendaraan->tahun ?? false)
                    <span>• {{ $kendaraan->tahun }}</span>
                @endif
                @if($kendaraan->pengemudi ?? false)
                    <span>• {{ $kendaraan->pengemudi }}</span>
                @endif
            </p>

            {{-- INFO BOX --}}
            <div class="info-box">

                <div class="info-item">
                    <div class="info-icon blue">🚘</div>
                    <div>
                        <p class="info-label">Jenis Kendaraan</p>
                        <h4>{{ ucfirst($kendaraan->jenis_kendaraan) }}</h4>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon green">👥</div>
                    <div>
                        <p class="info-label">Kapasitas</p>
                        <h4>{{ $kendaraan->kapasitas }} Orang</h4>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon orange">📍</div>
                    <div>
                        <p class="info-label">Fasilitas</p>
                        <h4>
                            @php
                                $facList = is_array($kendaraan->fasilitas)
                                    ? $kendaraan->fasilitas
                                    : array_filter(preg_split('/[\r\n,]+/', $kendaraan->fasilitas ?? ''));
                            @endphp
                            {{ count($facList) }}+ Item
                        </h4>
                    </div>
                </div>

            </div>

            {{-- TAB SWITCHER --}}
            <div class="tab-switcher">
                <button class="tab-btn active" onclick="showTab('tab-ketentuan', this)">
                    Ketentuan Sewa
                </button>
                <button class="tab-btn" onclick="showTab('tab-tentang', this)">
                    Tentang Kendaraan
                </button>
            </div>

            {{-- TAB: KETENTUAN --}}
            <div class="tab-content active" id="tab-ketentuan">
                <div class="content-card">
                    <h3>Ketentuan Sewa</h3>
                    @if(!empty($kendaraan->ketentuan))
                        @php
                            $ketentuan = preg_split('/\r\n|\r|\n/', $kendaraan->ketentuan);
                            $ketentuan = array_filter($ketentuan);
                        @endphp
                        <ul class="check-list">
                            @foreach($ketentuan as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{-- Default ketentuan jika kolom belum ada --}}
                        <ul class="check-list">
                            <li>Termasuk Sopir Berpengalaman</li>
                            <li>Tidak Ada Biaya Lajur</li>
                            <li>Harus SIM B1 / B2 (Opsional)</li>
                            <li>Dalam Termasuk Parkir &amp; Tips Sopir</li>
                            <li>Air Mineral &amp; Fasilitas Kendaraan</li>
                            <li>Belum Termasuk Konsumsi Jarak Jauh</li>
                        </ul>
                    @endif
                </div>
            </div>

            {{-- TAB: TENTANG --}}
            <div class="tab-content" id="tab-tentang">
                <div class="content-card">
                    <h3>Tentang Kendaraan</h3>
                    @if($kendaraan->deskripsi)
                        <p>{{ $kendaraan->deskripsi }}</p>
                    @else
                        <p>Kendaraan nyaman dan terawat, siap digunakan untuk berbagai keperluan perjalanan. Dilengkapi dengan fasilitas modern untuk kenyamanan penumpang.</p>
                    @endif

                    @if(!empty($facList))
                        <h3 style="margin-top:20px">Fasilitas Kendaraan</h3>
                        <ul class="check-list">
                            @foreach($facList as $fac)
                                <li>{{ trim($fac) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>

        {{-- ========================
             KOLOM KANAN (BOOKING)
        ======================== --}}
        <div class="detail-right">

            <div class="booking-card">

                <p class="price-label">Harga Sewa</p>
                <h2>Rp {{ number_format($kendaraan->harga_sewa, 0, ',', '.') }}</h2>
                <p class="per-hari">/hari</p>

                {{-- FORM BOOKING --}}
                <form action="{{ route('dashboard.user.booking.check') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggalMulai"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="tanggalSelesai"
                                min="{{ date('Y-m-d', strtotime('+2 day')) }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Penumpang</label>
                        <select name="jumlah_penumpang" id="jumlahPenumpang">
                            @for($i = 1; $i <= $kendaraan->kapasitas; $i++)
                                <option value="{{ $i }}">{{ $i }} Orang</option>
                            @endfor
                        </select>
                    </div>

                    {{-- RINGKASAN HARGA --}}
                    <div class="price-summary" id="priceSummary" style="display:none">
                        <div class="price-row">
                            <span id="priceDesc">Rp 0 × 0 hari</span>
                            <span id="priceSubtotal">Rp 0</span>
                        </div>
                        <div class="price-row">
                            <span>Biaya Layanan</span>
                            <span id="priceLayanan">Rp 0</span>
                        </div>
                        <div class="price-row">
                            <span>Total</span>
                            <span id="priceTotal">Rp 0</span>
                        </div>
                    </div>

                    <button type="button" class="btn-booking-main" onclick="pesanSekarang()">
                        Pesan Sekarang
                    </button>

                </form>

                <p class="booking-note">Konfirmasi akan dikirim ke email Anda</p>

                <ul class="benefit-list">
                    <li>✅ Kendaraan Terawat &amp; Bersih</li>
                    <li>✅ Driver Berpengalaman</li>
                    <li>✅ Harga Transparan</li>
                    <li>✅ Pembatalan Mudah</li>
                </ul>

                <hr>

                <h3>Butuh Bantuan?</h3>
                <p class="help-text">Hubungi kami untuk informasi lebih lanjut</p>

                @php
                    $pesanWa = urlencode(
                        "Halo Admin,\n\n"
                        . "Saya tertarik dengan kendaraan:\n"
                        . "Nama: " . $kendaraan->nama_kendaraan . "\n"
                        . "Jenis: " . $kendaraan->jenis_kendaraan . "\n"
                        . "Harga: Rp " . number_format($kendaraan->harga_sewa, 0, ',', '.') . "/hari\n\n"
                        . "Apakah masih tersedia?"
                    );
                @endphp

                <a
                    href="https://wa.me/6282140360481?text={{ $pesanWa }}"
                    target="_blank"
                    class="btn-service">
                    💬 Hubungi Customer Service
                </a>

            </div>

        </div>

    </div>
</section>

{{-- ========================
     PILIHAN SERUPA
======================== --}}
@if(isset($kendaraanSerupa) && $kendaraanSerupa->count() > 0)
<section class="similar-section">

    <div class="similar-header">
        <div>
            <h2>Pilihan Serupa</h2>
            <p class="similar-sub">Kendaraan premium lain yang mungkin cocok untuk Anda</p>
        </div>
        <a href="{{ route('dashboard.user.katalogkendaraan') }}">Lihat Semua →</a>
    </div>

    <div class="similar-grid">
        @foreach($kendaraanSerupa->take(3) as $serupa)
        <a href="{{ route('dashboard.user.detailkendaraan', $serupa->id_kendaraan) }}" class="similar-card">

            <img
                src="{{ $serupa->foto_kendaraan
                    ? asset('storage/' . $serupa->foto_kendaraan)
                    : asset('img/default.png') }}"
                alt="{{ $serupa->nama_kendaraan }}">

            <div class="similar-body">
                <div class="similar-name">{{ $serupa->nama_kendaraan }}</div>
                <div class="similar-meta">
                    <span>👥 {{ $serupa->kapasitas }}</span>
                    <span>🚘 {{ ucfirst($serupa->jenis_kendaraan) }}</span>
                </div>
                <div class="similar-price">
                    Rp {{ number_format($serupa->harga_sewa, 0, ',', '.') }}
                    <span>/hari</span>
                </div>
            </div>

        </a>
        @endforeach
    </div>

</section>
@endif

@endsection

@push('scripts')
<script>
// ── Tab switcher ──────────────────────────────
function showTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    element.classList.add('active');
}

// ── Harga dinamis ─────────────────────────────
(function () {
    const hargaPerHari  = {{ $kendaraan->harga_sewa }};
    const biayaLayanan  = 50000; // sesuaikan
    const mulaiInput    = document.getElementById('tanggalMulai');
    const selesaiInput  = document.getElementById('tanggalSelesai');
    const summary       = document.getElementById('priceSummary');

    function formatRp(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function hitungHarga() {
        const mulai  = new Date(mulaiInput.value);
        const selesai = new Date(selesaiInput.value);

        if (!mulaiInput.value || !selesaiInput.value || selesai <= mulai) {
            summary.style.display = 'none';
            return;
        }

        const diffMs   = selesai - mulai;
        const hari     = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
        const subtotal = hargaPerHari * hari;
        const total    = subtotal + biayaLayanan;

        document.getElementById('priceDesc').textContent     = formatRp(hargaPerHari) + ' × ' + hari + ' hari';
        document.getElementById('priceSubtotal').textContent = formatRp(subtotal);
        document.getElementById('priceLayanan').textContent  = formatRp(biayaLayanan);
        document.getElementById('priceTotal').textContent    = formatRp(total);
        summary.style.display = 'block';
    }

    mulaiInput.addEventListener('change', function () {
        // Pastikan tanggal selesai >= tanggal mulai + 1
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        selesaiInput.min = nextDay.toISOString().split('T')[0];
        if (selesaiInput.value && selesaiInput.value <= this.value) {
            selesaiInput.value = nextDay.toISOString().split('T')[0];
        }
        hitungHarga();
    });

    selesaiInput.addEventListener('change', hitungHarga);
})();

// ── Pesan Sekarang ────────────────────────────
function pesanSekarang() {
    const tanggalMulai    = document.getElementById('tanggalMulai').value;
    const tanggalSelesai  = document.getElementById('tanggalSelesai').value;
    const jumlahPenumpang = document.getElementById('jumlahPenumpang').value;

    if (!tanggalMulai || !tanggalSelesai) {
        alert('Harap isi tanggal mulai dan tanggal selesai terlebih dahulu.');
        return;
    }

    const baseUrl = "{{ route('dashboard.user.booking-kendaraan', $kendaraan->id_kendaraan) }}";
    const url = `${baseUrl}?tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}&jumlah_peserta=${jumlahPenumpang}`;
    window.location.href = url;
}
</script>
@endpush