@extends('layouts.user')

@section('title', 'Booking')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/booking.css') }}">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')

 <main class="booking-container">

@if(!empty($showWarning) && $showWarning)
<div class="booking-popup-overlay">
    <div class="booking-popup-box">
        <div class="booking-popup-icon">
            <i class="fa-regular fa-folder-open"></i>
        </div>

        <h2>Belum ada paket wisata yang dipilih</h2>
        <p>
            Untuk melanjutkan ke proses booking, silakan pilih paket wisata terlebih dahulu.
        </p>

        <div class="booking-popup-actions">
            <a href="{{ route('dashboard.user.katalogpaketwisata') }}" class="btn-popup-primary">
                Pilih Paket Wisata
            </a>

            <a href="{{ route('dashboard.user') }}" class="btn-popup-secondary">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endif
 
        {{-- ================= FORM AWAL BOOKING ================= --}}
        @if($page === 'booking')
        <section class="booking-page">
            <h1 class="page-title">Form Booking</h1>

             <form action="{{ route('dashboard.user.booking.check') }}" method="POST">
        @csrf
            <div class="booking-grid">
                <div class="booking-left">
                    <div class="booking-card">
                        <h3 class="card-title">Informasi Kontak</h3>
                        <div class="form-group">
                            <label>Nama Lengkap <span>*</span></label>
                            <div class="input-box">
                                <input type="text" value="{{ $user->nama ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email <span>*</span></label>
                            <div class="input-box with-icon">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="text" value="{{ $user->email ?? '' }}" readonly>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label>No KTP <span>*</span></label>
                        <div class="input-box {{ $errors->has('no_ktp') ? 'input-error' : '' }}">
                            <input
                                type="text"
                                name="no_ktp"
                                placeholder="Masukkan No KTP"
                                value="{{ old('no_ktp') }}"
                                maxlength="16"
                                inputmode="numeric"
                            >
                        </div>

                        @error('no_ktp')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                        </div>

                        <div class="form-group no-margin">
                            <label>No Telepon <span>*</span></label>
                            <div class="input-box with-icon">
                                <i class="fa-solid fa-phone"></i>
                               <input type="text" value="{{ $user->no_hp ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                  <div class="booking-card">
                        
                    <div class="form-group">
                            <label>Jumlah Peserta <span>*</span></label>
                        <div class="input-box with-icon {{ $errors->has('jumlah_peserta') ? 'input-error' : '' }}">
                            <i class="fa-regular fa-user"></i>
                            <input type="number" id="jumlah_peserta" name="jumlah_peserta" value="{{ old('jumlah_peserta', 1) }}" min="1">
                        </div>

                        @error('jumlah_peserta')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>
                     

                        <div class="form-group no-margin">
                            <label>Catatan (opsional)</label>
                            <div class="input-box">
                               <input type="text" name="catatan" placeholder="Tulis request anda disini">
                            </div>
                        </div>
                    </div>
                    

                    <div class="booking-card payment-card">
                    <h3 class="card-title">Metode Pembayaran</h3>

                    <div class="payment-method qris selected" id="qrisToggle" style="cursor: pointer;">
                        <div class="payment-icon qr-icon">▦</div>
                        <div class="payment-text">
                            <strong>QRIS</strong>
                            <span>Bayar dengan scan QRIS</span>
                        </div>
                    </div>

    <div id="opsiPembayaranQris" style="display: none; margin-top: 14px;">
        <div class="payment-method payment-radio" id="pilihDp" style="cursor: pointer;">
            <div class="radio" id="radioDp"></div>
            <div class="payment-text single-line">
                <strong>DP (Down Payment)</strong>
            </div>
        </div>

        <div class="payment-method payment-radio" id="pilihPelunasan" style="cursor: pointer; margin-top: 10px;">
            <div class="radio" id="radioPelunasan"></div>
            <div class="payment-text single-line">
                <strong>Pelunasan</strong>
            </div>
        </div>
    </div>

    <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" value="{{ old('metode_pembayaran') }}">

    @error('metode_pembayaran')
        <small class="error-text">{{ $message }}</small>
    @enderror
</div>
</div>

               

                    


  {{-- ================= RINGKASAN BOOKING ================= --}}


            
            <div class="booking-right">
    <div class="summary-card">
        <h3 class="summary-title">Ringkasan Booking</h3>

        @if($paket)
           <h4 class="summary-package">{{ ucwords($paket->nama_paket) }}</h4>
        @else
            <h4 class="summary-package">Paket belum dipilih</h4>
        @endif

        <div class="summary-meta">
            <div>
                <i class="fa-regular fa-calendar"></i>
                {{ $paket->durasi ?? '-' }}
            </div>
            <div>
                <i class="fa-solid fa-user"></i>
                <span id="jumlahPesertaText">1</span> Peserta
            </div>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-price-list">
            <div class="summary-row">
                <span>Harga per orang</span>
                <strong>
                    @if($paket)
                        Rp {{ number_format($paket->harga, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </strong>
            </div>

            <div class="summary-row">
                <span>Jumlah peserta</span>
                <strong>x<span id="jumlahPesertaKali">1</span></strong>
            </div>

            <div class="summary-row">
                <span>Total Harga</span>
                <strong>
                    Rp <span id="totalHarga">
                        @if($paket)
                            {{ number_format($paket->harga, 0, ',', '.') }}
                        @else
                            0
                        @endif
                    </span>
                </strong>
            </div>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-total">
    <span id="labelTotalPembayaran">Total pembayaran</span>
    <strong class="total-payment-value">
        <span class="currency">Rp</span>
        <span id="totalPembayaran">
            @if($paket)
                {{ number_format($paket->harga, 0, ',', '.') }}
            @else
                0
            @endif
        </span>
    </strong>
</div>


<button type="submit" class="pay-btn" style="border: none; cursor: pointer; width: 100%;">
    <i class="fa-solid fa-credit-card"></i>
    Konfirmasi & Bayar
</button>

        <div class="summary-notes">
            <div><i class="fa-solid fa-circle-check"></i> Konfirmasi booking dalam 24 Jam</div>
            <div><i class="fa-solid fa-circle-check"></i> Pembatalan Gratis 24 Jam</div>
            <div><i class="fa-solid fa-circle-check"></i> Dijamin Harga Terbaik</div>
        </div>
    </div>
</div>
</div>
</form>
        </section>
        @endif

       


        {{-- ================= HALAMAN QRIS ================= --}}
        @if($page === 'qris')
        <section class="qris-page">
            <h1 class="qris-title">Kode Pembayaran</h1>

            <div class="qris-card">
                <div class="qris-label">QRIS</div>
                <img src="{{ asset('img/qris.jpg') }}" alt="QRIS" class="qris-image">
                <button class="save-qr-btn">
                    <i class="fa-solid fa-download"></i>
                    Simpan kode QR
                </button>
            </div>

            <div class="expired-box">
                <div class="expired-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>

                <div class="expired-content">
                    <div class="expired-top">
                        <span>Pembayaran Kadaluarsa dalam</span>
                        <strong>23 : 59 : 58</strong>
                    </div>
                    <p>Jatuh tempo pada <b>14:20, 5 Maret 2026</b></p>
                    <small>Booking akan otomatis dibatalkan jika waktu pembayaran habis</small>
                </div>
            </div>

            <div class="info-card">
                <h3>Informasi Booking</h3>

                <div class="info-row"><span>ID Booking</span><strong>BK001</strong></div>
                <div class="info-row"><span>Paket Wisata</span><strong>Paket Wisata Pantai Watu Karung</strong></div>
                <div class="info-row"><span>Jumlah Peserta</span><strong>2 orang</strong></div>
                <div class="info-row"><span>Tanggal Keberangkatan</span><strong>17 Maret 2026</strong></div>
                <div class="info-row"><span>Tanggal Kembali</span><strong>17 Maret 2026</strong></div>
            </div>

            <div class="info-card">
                <h3>Ringkasan Pembayaran</h3>

                <div class="info-row"><span>Metode Pembayaran</span><strong>Qris - Lunas</strong></div>
                <div class="info-row"><span>Total Pembayaran</span><strong>Rp 3.000.000</strong></div>
            </div>
        </section>
        @endif


        {{-- ================= HALAMAN SUKSES ================= --}}
        @if($page === 'success')
        <section class="success-page">
            <div class="success-icon">
                <i class="fa-regular fa-circle-check"></i>
            </div>

            <h1>Pembayaran Berhasil Dikirim!</h1>

            <p>Pembayaran anda telah kami terima dan akan segera diverifikasi.</p>
            <p>Kami akan mengirimkan konfirmasi melalui email dalam waktu 1x24 jam.</p>
        </section>
        @endif

    </main>

@endsection


{{-- ===================== SCRIPT ================= --}}
@push('scripts')
@if($paket)
<script>
    const hargaPaket = {{ $paket->harga ?? 0 }};

    const jumlahPesertaInput = document.getElementById('jumlah_peserta');
    const jumlahPesertaText = document.getElementById('jumlahPesertaText');
    const jumlahPesertaKali = document.getElementById('jumlahPesertaKali');
    const totalHarga = document.getElementById('totalHarga');
    const totalPembayaran = document.getElementById('totalPembayaran');
    const labelTotalPembayaran = document.getElementById('labelTotalPembayaran');

    const qrisToggle = document.getElementById('qrisToggle');
    const opsiPembayaranQris = document.getElementById('opsiPembayaranQris');
    const pilihDp = document.getElementById('pilihDp');
    const pilihPelunasan = document.getElementById('pilihPelunasan');
    const radioDp = document.getElementById('radioDp');
    const radioPelunasan = document.getElementById('radioPelunasan');
    const metodePembayaran = document.getElementById('metode_pembayaran');

    let metodeTerpilih = "{{ old('metode_pembayaran', '') }}";

    function hitungTotalAsli() {
        let jumlah = parseInt(jumlahPesertaInput.value) || 1;

        if (jumlah < 1) {
            jumlah = 1;
            jumlahPesertaInput.value = 1;
        }

        return hargaPaket * jumlah;
    }

    function updateRingkasanBooking() {
        if (!jumlahPesertaInput) return;

        const jumlah = parseInt(jumlahPesertaInput.value) || 1;
        const total = hitungTotalAsli();

        if (jumlahPesertaText) jumlahPesertaText.textContent = jumlah;
        if (jumlahPesertaKali) jumlahPesertaKali.textContent = jumlah;
        if (totalHarga) totalHarga.textContent = total.toLocaleString('id-ID');

        if (metodeTerpilih === 'dp') {
    const totalDp = total * 0.5;
    if (labelTotalPembayaran) labelTotalPembayaran.textContent = 'Total DP yang harus dibayar (50%)';
    if (totalPembayaran) totalPembayaran.textContent = totalDp.toLocaleString('id-ID');
} else {
    if (labelTotalPembayaran) labelTotalPembayaran.textContent = 'Total pembayaran';
    if (totalPembayaran) totalPembayaran.textContent = total.toLocaleString('id-ID');
}
    }

    if (qrisToggle) {
        qrisToggle.addEventListener('click', function () {
            if (opsiPembayaranQris.style.display === 'none' || opsiPembayaranQris.style.display === '') {
                opsiPembayaranQris.style.display = 'block';
            } else {
                opsiPembayaranQris.style.display = 'none';
            }
        });
    }

    if (pilihDp) {
        pilihDp.addEventListener('click', function () {
            metodeTerpilih = 'dp';
            metodePembayaran.value = 'dp';

            radioDp.classList.add('active');
            radioPelunasan.classList.remove('active');

            pilihDp.classList.add('selected');
            pilihPelunasan.classList.remove('selected');

            updateRingkasanBooking();
        });
    }

    if (pilihPelunasan) {
        pilihPelunasan.addEventListener('click', function () {
            metodeTerpilih = 'pelunasan';
            metodePembayaran.value = 'pelunasan';

            radioPelunasan.classList.add('active');
            radioDp.classList.remove('active');

            pilihPelunasan.classList.add('selected');
            pilihDp.classList.remove('selected');

            updateRingkasanBooking();
        });
    }

    if (jumlahPesertaInput) {
    jumlahPesertaInput.addEventListener('input', updateRingkasanBooking);
}

if (metodeTerpilih === 'dp') {
    if (opsiPembayaranQris) opsiPembayaranQris.style.display = 'block';
    if (radioDp) radioDp.classList.add('active');
    if (radioPelunasan) radioPelunasan.classList.remove('active');
    if (pilihDp) pilihDp.classList.add('selected');
    if (pilihPelunasan) pilihPelunasan.classList.remove('selected');
    if (metodePembayaran) metodePembayaran.value = 'dp';
}

if (metodeTerpilih === 'pelunasan') {
    if (opsiPembayaranQris) opsiPembayaranQris.style.display = 'block';
    if (radioPelunasan) radioPelunasan.classList.add('active');
    if (radioDp) radioDp.classList.remove('active');
    if (pilihPelunasan) pilihPelunasan.classList.add('selected');
    if (pilihDp) pilihDp.classList.remove('selected');
    if (metodePembayaran) metodePembayaran.value = 'pelunasan';
}

updateRingkasanBooking();

    
</script>
@endif
@endpush

