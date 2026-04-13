@extends('layouts.user')

@section('title', 'Request Booking')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/requestbooking.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('navbar_action')
    <a href="{{ route('dashboard.user') }}" class="btn-kembali">← Kembali</a>
@endsection

@section('content')

@if($step == 'home')
<section class="request-home">
    <section class="hero-title">
        <h1>Request Booking</h1>
        <p>Buat Perjalananmu Sendiri!</p>
    </section>

    <section class="request-steps">
        <div class="steps-container">
            <div class="step-card">
                <div class="step-icon">📍</div>
                <h3>Pilih Destinasi</h3>
                <p>Tentukan tempat wisata yang ingin anda kunjungi</p>
            </div>
            <div class="step-card">
                <div class="step-icon">📅</div>
                <h3>Atur Jadwal</h3>
                <p>Pilih tanggal dan durasi perjalanan Anda</p>
            </div>
            <div class="step-card">
                <div class="step-icon">🚐</div>
                <h3>Transportasi</h3>
                <p>Pilih Kendaraan sesuai jumlah peserta</p>
            </div>
        </div>
        <div class="request-action">
            <a href="{{ route('dashboard.user.requestbooking', 'informasi') }}" class="btn-mulai">
                Mulai Rencanakan Perjalanan <span>→</span>
            </a>
        </div>
    </section>
</section>
@endif

@if($step != 'home')
<main class="request-form-page">
    <section class="step-navigation">
        <div class="step-list">
            <div class="step-item {{ $step == 'informasi' ? 'active' : '' }}">
                <i class="fa-regular fa-file-lines"></i>
                <span>Informasi Perjalanan</span>
            </div>
            <div class="step-item {{ $step == 'destinasi' ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot"></i>
                <span>Pilih Tujuan</span>
            </div>
            <div class="step-item {{ $step == 'ringkasan' ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Ringkasan Perjalanan</span>
            </div>
            <div class="step-item {{ $step == 'request' ? 'active' : '' }}">
                <i class="fa-regular fa-paper-plane"></i>
                <span>Request Booking</span>
            </div>
        </div>
    </section>
@endif

{{-- ===================== STEP INFORMASI ===================== --}}
@if($step == 'informasi')
<section class="info-form-section">
    <form action="{{ route('dashboard.user.requestbooking.informasi.store') }}" method="POST">
        @csrf

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        {{-- Data dari profil dikirim via hidden --}}
        <input type="hidden" name="nama_lengkap" value="{{ Auth::user()->nama }}">
        <input type="hidden" name="email" value="{{ Auth::user()->email }}">
        <input type="hidden" name="no_telepon" value="{{ Auth::user()->no_hp }}">

        <div class="info-form-grid">
            <div class="form-card">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-box">
                        <input type="text" value="{{ Auth::user()->nama }}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-box with-icon">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" value="{{ Auth::user()->email }}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>No KTP <span>*</span></label>
                    <div class="input-box">
                        <input type="text" name="no_ktp" value="{{ old('no_ktp', session('request_booking.informasi.no_ktp')) }}" placeholder="Masukkan No KTP">
                    </div>
                </div>

                <div class="form-group">
                    <label>No Telepon</label>
                    <div class="input-box with-icon">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" value="{{ Auth::user()->no_hp }}" disabled>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="double-column">
                    <div class="form-group">
                        <label>Tanggal Keberangkatan <span>*</span></label>
                        <div class="input-box with-icon">
                            <i class="fa-regular fa-calendar"></i>
                            <input type="date" name="tanggal_keberangkatan" value="{{ old('tanggal_keberangkatan', session('request_booking.informasi.tanggal_keberangkatan')) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Kembali <span>*</span></label>
                        <div class="input-box with-icon">
                            <i class="fa-regular fa-calendar"></i>
                            <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali', session('request_booking.informasi.tanggal_kembali')) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jumlah Peserta <span>*</span></label>
                    <div class="input-box with-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="number" name="jumlah_peserta" min="1" value="{{ old('jumlah_peserta', session('request_booking.informasi.jumlah_peserta')) }}" placeholder="Masukkan jumlah peserta">
                    </div>
                </div>
            </div>
        </div>

        <div class="next-button-wrapper">
            <button type="submit" class="btn-lanjut">Lanjut <span>→</span></button>
        </div>
    </form>
</section>
@endif

{{-- ===================== STEP DESTINASI ===================== --}}
@if($step == 'destinasi')
<section class="destinasi-page">
    <form action="{{ route('dashboard.user.requestbooking.destinasi.store') }}" method="POST">
        @csrf

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="destinasi-card">
            <div class="destinasi-top-filter">
                <div class="filter-group">
                    <label>Provinsi Tujuan <span>*</span></label>
                    <div class="select-box">
                        <select name="provinsi" id="selectProvinsiTujuan" onchange="loadKota(this.value)">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->nama_provinsi }}"
                                    {{ old('provinsi', session('request_booking.destinasi.provinsi')) == $provinsi->nama_provinsi ? 'selected' : '' }}>
                                    {{ $provinsi->nama_provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Kota Tujuan <span>*</span></label>
                    <div class="select-box">
                        <select name="kota_tujuan" id="selectKotaTujuan">
                            <option value="">Pilih Kota</option>
                            @if(session('request_booking.destinasi.kota_tujuan'))
                                <option value="{{ session('request_booking.destinasi.kota_tujuan') }}" selected>
                                    {{ session('request_booking.destinasi.kota_tujuan') }}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="destinasi-double-input">
                <div class="filter-group">
                    <label>Provinsi Asal <span>*</span></label>
                    <div class="select-box">
                        <select name="provinsi_input" id="selectProvinsiAsal" onchange="loadKotaAsal(this.value)">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->nama_provinsi }}"
                                    {{ old('provinsi_input', session('request_booking.destinasi.provinsi_input')) == $provinsi->nama_provinsi ? 'selected' : '' }}>
                                    {{ $provinsi->nama_provinsi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Kota Asal <span>*</span></label>
                    <div class="select-box">
                        <select name="kota_asal" id="selectKotaAsal">
                            <option value="">Pilih Kota</option>
                            @if(session('request_booking.destinasi.kota_asal'))
                                <option value="{{ session('request_booking.destinasi.kota_asal') }}" selected>
                                    {{ session('request_booking.destinasi.kota_asal') }}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Titik Jemput <span>*</span></label>
                    <div class="select-box">
                        <input type="text" name="titik_jemput"
                            value="{{ old('titik_jemput', session('request_booking.destinasi.titik_jemput')) }}"
                            placeholder="Contoh: Depan Alfamart Jl. Sudirman">
                    </div>
                </div>

                <div class="filter-group">
                    <label>Alamat Lengkap <span>*</span></label>
                    <div class="select-box">
                        <input type="text" name="alamat"
                            value="{{ old('alamat', session('request_booking.destinasi.alamat')) }}"
                            placeholder="Masukkan alamat lengkap">
                    </div>
                </div>
            </div>

            <div class="catatan-group">
                <label>Catatan</label>
                <textarea name="catatan" placeholder="Tulis catatan tambahan...">{{ old('catatan', session('request_booking.destinasi.catatan')) }}</textarea>
            </div>
        </div>

        <div class="destinasi-bottom-action">
            <a href="{{ route('dashboard.user.requestbooking', 'informasi') }}" class="btn-kembali-step">← Kembali</a>
            <button type="submit" class="btn-lanjut-step">Lanjut →</button>
        </div>
    </form>
</section>
@endif

{{-- ===================== STEP RINGKASAN ===================== --}}
@php
    $informasi    = session('request_booking.informasi', []);
    $destinasi    = session('request_booking.destinasi', []);
    $tglBerangkat = $informasi['tanggal_keberangkatan'] ?? '-';
    $tglKembali   = $informasi['tanggal_kembali'] ?? '-';
    $durasi = '-';
    if ($tglBerangkat !== '-' && $tglKembali !== '-') {
        $selisih = \Carbon\Carbon::parse($tglBerangkat)->diffInDays(\Carbon\Carbon::parse($tglKembali));
        $durasi  = $selisih . ' Hari ' . $selisih . ' Malam';
    }
    $namaUser  = Auth::user()->nama  ?? '-';
    $emailUser = Auth::user()->email ?? '-';
    $noHpUser  = Auth::user()->no_hp ?? '-';
@endphp

@if($step == 'ringkasan')
<section class="ringkasan-page">
    <div class="ringkasan-card">
        <h2 class="ringkasan-title">Ringkasan Perjalanan</h2>

        <div class="ringkasan-info-grid">
            <div class="ringkasan-info-left">
                <div class="info-row">
                    <small>Nama Lengkap</small>
                    <p>{{ $namaUser }}</p>
                </div>
                <div class="info-row">
                    <small>Email</small>
                    <p>{{ $emailUser }}</p>
                </div>
                <div class="info-row">
                    <small>No KTP</small>
                    <p>{{ $informasi['no_ktp'] ?? '-' }}</p>
                </div>
                <div class="info-row">
                    <small>No Telepon</small>
                    <p>{{ $noHpUser }}</p>
                </div>
                <div class="info-row">
                    <small>Tanggal Keberangkatan</small>
                    <p>{{ $tglBerangkat }}</p>
                </div>
                <div class="info-row">
                    <small>Tanggal Kembali</small>
                    <p>{{ $tglKembali }}</p>
                </div>
                <div class="info-row">
                    <small>Jumlah Peserta</small>
                    <p>{{ $informasi['jumlah_peserta'] ?? '-' }} orang</p>
                </div>
            </div>

            <div class="ringkasan-info-right">
                <div class="info-row">
                    <small>Provinsi Asal</small>
                    <p>{{ $destinasi['provinsi_input'] ?? '-' }}</p>
                </div>

                <div class="info-row">
                    <small>Kota Asal</small>
                    <p>{{ $destinasi['kota_asal'] ?? '-' }}</p>
                </div>

                <div class="info-row">
                    <small>Titik Jemput</small>
                    <p>{{ $destinasi['titik_jemput'] ?? '-' }}</p>
                </div>

                <div class="info-row">
                    <small>Alamat Lengkap</small>
                    <p>{{ $destinasi['alamat'] ?? '-' }}</p>
                </div>

                <div class="info-row">
                    <small>Durasi</small>
                    <p>{{ $durasi }}</p>
                </div>

                <div class="info-row">
                    <small>Catatan</small>
                    <p>{{ $destinasi['catatan'] ?? '-' }}</p>
                </div>

                <div class="info-row">
                    <small>Status</small>
                    <p>Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="ringkasan-bottom-action">
        <a href="{{ route('dashboard.user.requestbooking', 'destinasi') }}" class="btn-kembali-step">← Kembali</a>
        <form action="{{ route('dashboard.user.requestbooking.store') }}" method="POST">
        @csrf
        <button type="submit" class="btn-lanjut-step">Kirim Request →</button>
    </form>
    </div>
</section>
@endif

{{-- ===================== STEP REQUEST SUKSES ===================== --}}
@if($step == 'request')
<section class="request-success-page">
    <div class="request-success-card">
        <div class="request-success-header">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2>Request Berhasil Dikirim</h2>
        </div>

        <div class="request-success-body">
            <p class="success-main-text">Request Anda sedang diproses oleh admin.</p>
            <div class="status-box">
                <small>Status :</small>
                <div class="status-pill">MENUNGGU PERSETUJUAN</div>
            </div>
            <p class="success-info-text">
                Kami akan menghubungi Anda melalui email atau WhatsApp untuk konfirmasi lebih lanjut.
            </p>
            <p class="success-estimation">Estimasi waktu respon : 1-2 hari kerja</p>
        </div>
    </div>

    <div class="request-success-action">
        <a href="{{ route('dashboard.user.requestbooking', 'ringkasan') }}" class="btn-kembali-step">← Kembali</a>
        <a href="{{ route('dashboard.user') }}" class="btn-lanjut-step">Kembali Ke Dashboard</a>
    </div>
</section>
@endif

@if($step != 'home')
</main>
@endif

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
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
                        <p>📞 <span>085664837559</span></p>
                        <p>📷 <span>@myTranss_</span></p>
                        <p>🎵 <span>@Pariwisataku_</span></p>
                    </div>
                    <div class="contact-col">
                        <p>📍 <span>Alamat<br>Magetan, Jawa Timur, Indonesia</span></p>
                        <p>📧 <span>Email<br>mytransnusa@gmail.com</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            © 2026 <strong>MyTransPariwisata</strong>. All rights reserved.
        </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Load kota tujuan jika provinsi tujuan sudah terpilih
    const provinsiTujuan = document.getElementById('selectProvinsiTujuan');
    if (provinsiTujuan && provinsiTujuan.value) {
        loadKota(provinsiTujuan.value, '{{ session('request_booking.destinasi.kota_tujuan') }}');
    }

    // Load kota asal jika provinsi asal sudah terpilih
    const provinsiAsal = document.getElementById('selectProvinsiAsal');
    if (provinsiAsal && provinsiAsal.value) {
        loadKotaAsal(provinsiAsal.value, '{{ session('request_booking.destinasi.kota_asal') }}');
    }
});

function loadKota(namaProvinsi, selectedKota = '') {
    const selectKota = document.getElementById('selectKotaTujuan');
    if (!namaProvinsi) {
        selectKota.innerHTML = '<option value="">Pilih Kota</option>';
        return;
    }
    selectKota.innerHTML = '<option value="">Loading...</option>';
    fetch('/api/kota-by-provinsi/' + encodeURIComponent(namaProvinsi))
        .then(res => res.json())
        .then(kotas => {
            selectKota.innerHTML = '<option value="">Pilih Kota</option>';
            kotas.forEach(kota => {
                const option = document.createElement('option');
                option.value = kota.nama_kota;
                option.textContent = kota.nama_kota;
                if (kota.nama_kota === selectedKota) option.selected = true;
                selectKota.appendChild(option);
            });
        });
}

function loadKotaAsal(namaProvinsi, selectedKota = '') {
    const selectKota = document.getElementById('selectKotaAsal');
    if (!namaProvinsi) {
        selectKota.innerHTML = '<option value="">Pilih Kota</option>';
        return;
    }
    selectKota.innerHTML = '<option value="">Loading...</option>';
    fetch('/api/kota-by-provinsi/' + encodeURIComponent(namaProvinsi))
        .then(res => res.json())
        .then(kotas => {
            selectKota.innerHTML = '<option value="">Pilih Kota</option>';
            kotas.forEach(kota => {
                const option = document.createElement('option');
                option.value = kota.nama_kota;
                option.textContent = kota.nama_kota;
                if (kota.nama_kota === selectedKota) option.selected = true;
                selectKota.appendChild(option);
            });
        });
}
</script>
@endpush