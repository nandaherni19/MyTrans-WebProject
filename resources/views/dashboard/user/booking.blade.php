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

    @if($page === 'booking')
        <section class="booking-page">
            <h1 class="page-title">Form Booking</h1>

            @if(session('error'))
                <div style="background:#ffe5e5; color:#b00020; padding:10px; margin-bottom:15px; border-radius:8px;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fff3cd; color:#856404; padding:10px; margin-bottom:15px; border-radius:8px;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboard.user.booking.check') }}" method="POST">
                @csrf

                @if($paket)
                    <input type="hidden" name="id_paket" value="{{ $paket->id_paket }}">
                @endif

                @if($request)
                    <input type="hidden" name="id_request" value="{{ $request->id_request }}">
                @endif

                <div class="booking-grid">
                    <div class="booking-left">

                        <div class="booking-card">
                            <h3 class="card-title">Informasi Kontak</h3>

                            <div class="form-group">
                                <label>Nama Lengkap <span>*</span></label>
                                <div class="input-box">
                                    <input type="text" name="nama" value="{{ $user->nama ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email <span>*</span></label>
                                <div class="input-box with-icon">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="text" name="email" value="{{ $user->email ?? '' }}" readonly>
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
                                    <input type="text" name="no_hp" value="{{ $user->no_hp ?? '' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="booking-card">
                            <div class="form-group">
                                <label>Jumlah Peserta <span>*</span></label>
                                <div class="input-box with-icon {{ $errors->has('jumlah_peserta') ? 'input-error' : '' }}">
                                    <i class="fa-regular fa-user"></i>

                                    @if($request)
                                        <input
                                            type="number"
                                            id="jumlah_peserta"
                                            name="jumlah_peserta"
                                            value="{{ old('jumlah_peserta', $request->jumlah_peserta ?? 1) }}"
                                            min="1"
                                            readonly
                                        >
                                    @else
                                        <input
                                            type="number"
                                            id="jumlah_peserta"
                                            name="jumlah_peserta"
                                            value="{{ old('jumlah_peserta', 1) }}"
                                            min="1"
                                        >
                                    @endif
                                </div>

                                @error('jumlah_peserta')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            @if($paket)
                            <div class="form-group">
                                <label>Pilih Area / Kota Dilayani <span>*</span></label>
                                <div class="input-box">
                                    <select name="id_kota_layanan" required>
                                        <option value="">-- Pilih Area --</option>

                                        @forelse($paket->kotaLayanan as $kota)
                                            <option value="{{ $kota->id_kota }}" 
                                                {{ old('id_kota_layanan') == $kota->id_kota ? 'selected' : '' }}>
                                                {{ $kota->nama_kota }}
                                            </option>
                                        @empty
                                            <option value="">Tidak ada area tersedia</option>
                                        @endforelse

                                    </select>
                                </div>
                            </div>
                            @endif

                            @if($paket && $paket->tipe === 'open_trip' && $paket->titikJemput->isNotEmpty())
                            <div class="form-group">
                                <label>Pilih Titik Jemput <span>*</span></label>
                                <div class="input-box">
                                    <select name="id_titik_jemput" required>
                                        <option value="">-- Pilih Titik Jemput --</option>
                                        @foreach($paket->titikJemput as $titik)
                                            <option value="{{ $titik->id_titik_jemput }}" {{ old('id_titik_jemput') == $titik->id_titik_jemput ? 'selected' : '' }}>
                                                {{ $titik->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            @if($paket && $paket->tipe === 'paket')
                            <div class="form-group">
                                <label>Alamat Jemput <span>*</span></label>
                                <div class="input-box">
                                    <input 
                                        type="text" 
                                        name="alamat_jemput"
                                        placeholder="Contoh: alamat rumah / hotel"
                                        value="{{ old('alamat_jemput') }}"
                                        required
                                    >
                                </div>
                            </div>
                            @endif

                            @if($paket && $paket->tipe !== 'open_trip')
                            <div class="form-group">
                                <label>Tanggal Berangkat <span>*</span></label>
                                <div class="input-box">
                                    <input type="date" id="tanggal_berangkat" name="tanggal_berangkat" value="{{ old('tanggal_berangkat') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Kembali <span>*</span></label>
                                <div class="input-box">
                                    <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Kendaraan <span>*</span></label>
                                <p style="color:#6b7280; font-size:13px; margin-bottom:10px;">
                                    Bisa pilih lebih dari 1 jika kapasitas tidak cukup untuk semua peserta.
                                </p>

                                {{-- Dropdown pilih kendaraan --}}
                                <div style="display:flex; gap:10px; margin-bottom:10px;">
                                    <select id="selectKendaraanUser" style="flex:1; padding:10px 14px; border:1px solid #e5e7eb; border-radius:10px; font-size:13px; font-family:inherit; color:#374151; background:#f9fafb;">
                                        <option value="">-- Pilih Kendaraan --</option>
                                    </select>
                                    <button type="button" onclick="tambahKendaraanUser()"
                                        style="padding:10px 18px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                                        Tambah
                                    </button>
                                </div>

                                {{-- List kendaraan yang dipilih --}}
                                <div id="selectedKendaraanUser" style="display:flex; flex-direction:column; gap:8px; margin-bottom:10px;"></div>

                                {{-- Info total kapasitas --}}
                                <div style="display:flex; justify-content:space-between; font-size:13px; color:#6b7280; margin-top:4px;">
                                    <span>Total kapasitas terpilih: <strong id="totalKapasitasUser" style="color:#111827;">0</strong> orang</span>
                                    <span>Jumlah peserta: <strong id="infoJumlahPeserta" style="color:#2563eb;">-</strong> orang</span>
                                </div>

                                {{-- Warning jika kapasitas kurang --}}
                                <div id="warningKapasitas" style="display:none; margin-top:8px; padding:8px 12px; background:#fff3cd; color:#856404; border-radius:8px; font-size:13px;">
                                    ⚠️ Kapasitas kendaraan belum cukup untuk semua peserta!
                                </div>

                                {{-- Hidden inputs akan di-generate JS --}}
                                <div id="kendaraanHiddenInputs"></div>

                                @error('id_kendaraan')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>
                            @endif

                            <div class="form-group no-margin">
                                <label>Catatan (opsional)</label>
                                <div class="input-box">
                                    <input type="text" name="catatan" placeholder="Tulis request anda disini" value="{{ old('catatan') }}">
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
                                        <strong>Qris - DP (Down Payment)</strong>
                                    </div>
                                </div>

                                <div class="payment-method payment-radio" id="pilihPelunasan" style="cursor: pointer; margin-top: 10px;">
                                    <div class="radio" id="radioPelunasan"></div>
                                    <div class="payment-text single-line">
                                        <strong>Qris - Lunas</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- TAMBAHAN CASH --}}
                            <div class="payment-method cash" id="cashToggle" style="cursor: pointer;">
                                <div class="payment-icon cash-icon">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div class="payment-text">
                                    <strong>Cash</strong>
                                    <span>Bayar cash dengan konfirmasi admin</span>
                                </div>
                            </div>

                            <div id="opsiPembayaranCash" style="display: none; margin-top: 14px;">
                                <div class="payment-method payment-radio" id="pilihCashDp" style="cursor: pointer;">
                                    <div class="radio" id="radioCashDp"></div>
                                    <div class="payment-text single-line">
                                        <strong>Cash - DP (Down Payment)</strong>
                                    </div>
                                </div>

                                <div class="payment-method payment-radio" id="pilihCashPelunasan" style="cursor: pointer; margin-top: 10px;">
                                    <div class="radio" id="radioCashPelunasan"></div>
                                    <div class="payment-text single-line">
                                        <strong>Cash - Lunas</strong>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="tipe_pembayaran" id="tipe_pembayaran" value="{{ old('tipe_pembayaran') }}">
                            <input type="hidden" name="opsi_pembayaran" id="opsi_pembayaran" value="{{ old('opsi_pembayaran') }}">

                            @error('tipe_pembayaran')
                                <small class="error-text">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="booking-right">
                        <div class="summary-card">
                            <h3 class="summary-title">Ringkasan Booking</h3>

                            @if($paket)
                                <h4 class="summary-package">{{ ucwords($paket->nama_paket) }}</h4>
                            @elseif($request)
                                <h4 class="summary-package">Custom Trip (Request)</h4>
                            @endif

                            <div class="summary-meta">
                                <div>
                                    <i class="fa-regular fa-calendar"></i>
                                    @if($paket)
                                        {{ $paket->durasi }}
                                    @elseif($request)
                                        {{ $request->tanggal_keberangkatan }} - {{ $request->tanggal_kembali }}
                                    @endif
                                </div>
                                <div>
                                    <i class="fa-solid fa-user"></i>
                                    <span id="jumlahPesertaText">{{ old('jumlah_peserta', $request->jumlah_peserta ?? 1) }}</span> Peserta
                                </div>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-price-list">
                                @if($paket)
                                    <div class="summary-row">
                                        <span>Harga per orang</span>
                                        <strong>Rp {{ number_format($paket->harga, 0, ',', '.') }}</strong>
                                    </div>

                                    <div class="summary-row">
                                        <span>Jumlah peserta</span>
                                        <strong>x<span id="jumlahPesertaKali">{{ old('jumlah_peserta', 1) }}</span></strong>
                                    </div>
                                @elseif($request)
                                    <div class="summary-row">
                                        <span>Estimasi harga total</span>
                                        <strong>Rp {{ number_format($request->estimasi_harga ?? 0, 0, ',', '.') }}</strong>
                                    </div>

                                    <div class="summary-row">
                                        <small style="color:#666;">
                                            Sudah termasuk kendaraan dan kebutuhan perjalanan
                                        </small>
                                    </div>
                                @endif

                                <div class="summary-row">
                                    <span>Total Harga</span>
                                    <strong>
                                        Rp
                                        <span id="totalHarga">
                                            @if($paket)
                                                {{ number_format(($paket->harga ?? 0) * old('jumlah_peserta', 1), 0, ',', '.') }}
                                            @elseif($request)
                                                {{ number_format($request->estimasi_harga ?? 0, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </strong>
                                </div>
                            </div>

                            @if($request)
                                <div class="summary-divider"></div>

                                <div class="summary-row">
                                    <span>Kendaraan</span>
                                </div>

                                @php
                                    $groupedKendaraan = $request->kendaraans->groupBy('nama_kendaraan');
                                @endphp

                                @forelse($groupedKendaraan as $nama => $items)
                                    <div class="summary-row">
                                        <strong>{{ $nama }} x{{ $items->count() }}</strong>
                                    </div>
                                @empty
                                    <div class="summary-row">
                                        <small style="color:#666;">Belum ada kendaraan yang dipilih admin.</small>
                                    </div>
                                @endforelse
                            @endif

                            <div class="summary-divider"></div>

                            <div class="summary-total">
                                <span id="labelTotalPembayaran">Total pembayaran</span>
                                <strong class="total-payment-value">
                                    <span class="currency">Rp</span>
                                    <span id="totalPembayaran">
                                        @if($paket)
                                            {{ number_format(($paket->harga ?? 0) * old('jumlah_peserta', 1), 0, ',', '.') }}
                                        @elseif($request)
                                            {{ number_format($request->estimasi_harga ?? 0, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </strong>
                            </div>

                            <button
                                type="submit"
                                class="pay-btn"
                                id="btnBayar"
                                style="border: none; cursor: pointer; width: 100%;">
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

    @if($page === 'qris')
        <section class="qris-page">
            <h1 class="qris-title">Kode Pembayaran</h1>

            @if(!empty($successMessage))
                <div style="background:#e6ffed; color:#05603a; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #b7f5c5;">
                    <strong>Berhasil!</strong> {{ $successMessage }}
                </div>
            @endif

            <div id="successNotif"></div>

            <div class="qris-wrapper">
                <div class="qris-left">
                    <div class="expired-box">
                        <div class="expired-icon">
                            <i class="fa-regular fa-clock"></i>
                        </div>

                        <div class="expired-content">
                            <div class="expired-top">
                                <span>Pembayaran Kadaluarsa dalam</span>
                                <strong id="countdownPembayaran">--:--:--</strong>
                            </div>
                            <p>
                                Jatuh tempo pada
                                <b>
                                    {{ !empty($bookingData['expired_at']) ? \Carbon\Carbon::parse($bookingData['expired_at'])->format('H:i, d F Y') : '-' }}
                                </b>
                            </p>
                            <small>Booking akan otomatis dibatalkan jika waktu pembayaran habis</small>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Informasi Booking</h3>

                        <div class="info-row">
                            <span>ID Booking</span>
                            <strong>{{ $bookingData['id_booking'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Nama Lengkap</span>
                            <strong>{{ $bookingData['nama_lengkap'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Email</span>
                            <strong>{{ $bookingData['email'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Telepon</span>
                            <strong>{{ $bookingData['telepon'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Jumlah Peserta</span>
                            <strong>{{ $bookingData['jumlah_peserta'] ?? '-' }} orang</strong>
                        </div>

                        <div class="info-row">
                            <span>Catatan</span>
                            <strong>{{ $bookingData['catatan'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Detail Perjalanan</h3>

                        <div class="info-row">
                            <span>Paket Wisata</span>
                            <strong>{{ $bookingData['paket_wisata'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Jumlah Peserta</span>
                            <strong>{{ $bookingData['jumlah_peserta'] ?? '-' }} orang</strong>
                        </div>

                        <div class="info-row">
                            <span>Tanggal Keberangkatan</span>
                            <strong>{{ $bookingData['tanggal_berangkat'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Tanggal Kepulangan</span>
                            <strong>{{ $bookingData['tanggal_kembali'] ?? '-' }}</strong>
                        </div>

                        <div class="info-row">
                            <span>Kendaraan</span>
                            <strong>{{ $bookingData['kendaraan'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>Ringkasan Pembayaran</h3>

                        <div class="info-row">
                            <span>Metode Pembayaran</span>
                            <strong>
                                @php
                                $tipe = $bookingData['tipe_pembayaran'] ?? '';
                                $opsi = $bookingData['opsi_pembayaran'] ?? '';
                                $metode = $tipe . '_' . $opsi;
                                    $labelMetode = match($metode) {
                                        'qris_dp' => 'QRIS - DP',
                                        'qris_lunas' => 'QRIS - Pelunasan',
                                        'cash_dp' => 'Cash - DP',
                                        'cash_pelunasan' => 'Cash - Pelunasan',
                                        default => '-',
                                    };
                                @endphp

                                <div class="info-row">
                                    
                                    <strong>{{ $labelMetode }}</strong>
                                </div>
                            </strong>
                        </div>

                        <div class="info-row">
                            <span>Total Harga</span>
                            <strong>Rp {{ number_format($bookingData['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="info-row">
                            <span>Total Pembayaran</span>
                            <strong>Rp {{ number_format($bookingData['total_bayar'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    @if(isset($booking) && $booking->status_booking === 'dp_lunas')
                        <div style="margin-top:20px;">
                            <a
                                href="{{ route('dashboard.user.booking.pelunasan', $booking->id_booking) }}"
                                class="pay-btn"
                                style="display:block; text-align:center; text-decoration:none;">
                                Bayar Sisa Pelunasan
                            </a>
                        </div>
                    @endif
                </div>
                <div class="qris-right">
                    <div style="width:100%; max-width:420px;">
                        <div class="qris-card">
                            <div class="qris-label">QRIS</div>
                            @if(!empty($bookingData['qr_url']))
                                <img src="{{ $bookingData['qr_url'] }}" alt="QRIS Midtrans" class="qris-image">
                            @else
                                <p style="text-align:center; margin:20px 0;">QR belum tersedia</p>
                            @endif
                        </div>
                        <div class="info-card" style="margin-top:20px;">
                            <h3 style="font-size:22px; margin-bottom:14px;">Cara Bayar QRIS</h3>
                            <ol style="padding-left:20px; margin:0; color:#374151; line-height:1.8;">
                                <li>Buka aplikasi e-wallet atau mobile banking yang mendukung QRIS.</li>
                                <li>Pilih menu <strong>Scan QR</strong> atau <strong>Bayar QRIS</strong>.</li>
                                <li>Scan kode QR yang tampil di halaman ini.</li>
                                <li>Pastikan nominal pembayaran sudah sesuai.</li>
                                <li>Simpan bukti transaksi.</li>
                            </ol>
                            <p style="margin-top:14px; color:#6b7280; font-size:14px;">
                                Setelah pembayaran berhasil, status booking akan diperbarui oleh sistem.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif


    @if($page === 'cash')
    <section class="qris-page">
        <h1 class="qris-title">Instruksi Pembayaran Cash</h1>
        <div class="qris-wrapper">
            <!-- KIRI -->
            <div class="qris-left">
                <!-- INFORMASI BOOKING -->
                <div class="info-card">
                    <h3>Informasi Booking</h3>
                    <div class="info-row">
                        <span>ID Booking</span>
                        <strong>{{ $bookingData['id_booking'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Nama Lengkap</span>
                        <strong>{{ $bookingData['nama_lengkap'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Email</span>
                        <strong>{{ $bookingData['email'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Telepon</span>
                        <strong>{{ $bookingData['telepon'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Jumlah Peserta</span>
                        <strong>{{ $bookingData['jumlah_peserta'] ?? '-' }} orang</strong>
                    </div>

                    <div class="info-row">
                        <span>Catatan</span>
                        <strong>{{ $bookingData['catatan'] ?? '-' }}</strong>
                    </div>
                </div>

                <!-- DETAIL PERJALANAN -->
                <div class="info-card">
                    <h3>Detail Perjalanan</h3>

                    <div class="info-row">
                        <span>Paket Wisata</span>
                        <strong>{{ $bookingData['paket_wisata'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Tanggal Keberangkatan</span>
                        <strong>{{ $bookingData['tanggal_berangkat'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Tanggal Kepulangan</span>
                        <strong>{{ $bookingData['tanggal_kembali'] ?? '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Kendaraan</span>
                        <strong>{{ $bookingData['kendaraan'] ?? '-' }}</strong>
                    </div>
                </div>

                <!-- RINGKASAN -->
                <div class="info-card">
                    <h3>Ringkasan Pembayaran</h3>
                    <div class="info-row">
                        <span>Metode Pembayaran</span>
                        @php
                            $metode = $bookingData['metode_pembayaran'] ?? '';
                            $labelMetode = match($metode) {
                                'cash_dp' => 'Cash - DP',
                                'cash_pelunasan' => 'Cash - Lunas',
                                default => 'Cash',
                            };
                        @endphp
                        <strong>{{ $labelMetode }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Total Harga</span>
                        <strong>Rp {{ number_format($bookingData['total_harga'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="info-row">
                        <span>Total Pembayaran</span>
                        <strong>Rp {{ number_format($bookingData['total_bayar'] ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div class="qris-right">
                <div class="cash-box">
                    <div class="cash-icon">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <p class="cash-text">
                        Untuk pembayaran tunai, silakan hubungi admin kami melalui WhatsApp
                        untuk mendapatkan instruksi lebih lanjut.
                    </p>
                    <a href="https://wa.me/6285664837559?text=Halo admin, saya ingin konfirmasi booking ID {{ $bookingData['id_booking'] ?? '-' }}"
                        class="btn-wa">
                        <i class="fa-brands fa-whatsapp"></i>
                        Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrisToggle = document.getElementById('qrisToggle');
    const cashToggle = document.getElementById('cashToggle');

    const opsiQris = document.getElementById('opsiPembayaranQris');
    const opsiCash = document.getElementById('opsiPembayaranCash');

    const pilihDp = document.getElementById('pilihDp');
    const pilihPelunasan = document.getElementById('pilihPelunasan');
    const pilihCashDp = document.getElementById('pilihCashDp');
    const pilihCashPelunasan = document.getElementById('pilihCashPelunasan');

    const radioDp = document.getElementById('radioDp');
    const radioPelunasan = document.getElementById('radioPelunasan');
    const radioCashDp = document.getElementById('radioCashDp');
    const radioCashPelunasan = document.getElementById('radioCashPelunasan');

    const tipeInput = document.getElementById('tipe_pembayaran');
    const opsiInput = document.getElementById('opsi_pembayaran');

    const totalPembayaran = document.getElementById('totalPembayaran');
    const labelTotalPembayaran = document.getElementById('labelTotalPembayaran');

    const hargaPerOrang = {{ $paket ? ($paket->harga ?? 0) : 0 }};
const jumlahPesertaInput = document.getElementById('jumlah_peserta');
const jumlahPesertaText = document.getElementById('jumlahPesertaText');
const jumlahPesertaKali = document.getElementById('jumlahPesertaKali');
const totalHarga = document.getElementById('totalHarga');

let pilihanPembayaran = '';

function getTotalHargaAsli() {
    const jumlah = parseInt(jumlahPesertaInput.value) || 1;
    return hargaPerOrang * jumlah;
}

function updateRingkasan() {
    const jumlah = parseInt(jumlahPesertaInput?.value) || 1;
    const total = getTotalHargaAsli();

    if (jumlahPesertaText) jumlahPesertaText.innerText = jumlah;
    if (jumlahPesertaKali) jumlahPesertaKali.innerText = jumlah;
    if (totalHarga) totalHarga.innerText = formatRupiah(total);

    if (pilihanPembayaran === 'dp') {
        if (totalPembayaran) totalPembayaran.innerText = formatRupiah(total * 0.25);
        if (labelTotalPembayaran) labelTotalPembayaran.innerText = 'Total pembayaran DP 25%';
    } else {
        if (totalPembayaran) totalPembayaran.innerText = formatRupiah(total);
        if (labelTotalPembayaran) labelTotalPembayaran.innerText = 'Total pembayaran';
    }
}

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function resetRadio() {
        radioDp.classList.remove('active');
        radioPelunasan.classList.remove('active');
        radioCashDp.classList.remove('active');
        radioCashPelunasan.classList.remove('active');
    }

    function setDp() {
    pilihanPembayaran = 'dp';
    updateRingkasan();
}

function setLunas() {
    pilihanPembayaran = 'lunas';
    updateRingkasan();
}

jumlahPesertaInput.addEventListener('input', updateRingkasan);
updateRingkasan();

    qrisToggle.addEventListener('click', function () {
        opsiQris.style.display = 'block';
        opsiCash.style.display = 'none';
        tipeInput.value = 'qris';
        opsiInput.value = '';
        resetRadio();
        setLunas();
    });

    cashToggle.addEventListener('click', function () {
        opsiCash.style.display = 'block';
        opsiQris.style.display = 'none';
        tipeInput.value = 'cash';
        opsiInput.value = '';
        resetRadio();
        setLunas();
    });

    pilihDp.addEventListener('click', function () {
        resetRadio();
        radioDp.classList.add('active');
        tipeInput.value = 'qris';
        opsiInput.value = 'dp';
        setDp();
    });

    pilihPelunasan.addEventListener('click', function () {
        resetRadio();
        radioPelunasan.classList.add('active');
        tipeInput.value = 'qris';
        opsiInput.value = 'lunas';
        setLunas();
    });

    pilihCashDp.addEventListener('click', function () {
        resetRadio();
        radioCashDp.classList.add('active');
        tipeInput.value = 'cash';
        opsiInput.value = 'dp';
        setDp();
    });

    pilihCashPelunasan.addEventListener('click', function () {
        resetRadio();
        radioCashPelunasan.classList.add('active');
        tipeInput.value = 'cash';
        opsiInput.value = 'lunas';
        setLunas();
    });

    // ===== KENDARAAN DINAMIS USER =====
const tanggalBerangkatInput = document.getElementById('tanggal_berangkat');
const tanggalKembaliInput = document.getElementById('tanggal_kembali');
const selectKendaraanUser = document.getElementById('selectKendaraanUser');

let pilihanKendaraanUser = []; // [{id, nama, kapasitas}]
let semuaKendaraanUser = [];

function loadKendaraanUser() {
    if (!tanggalBerangkatInput || !tanggalKembaliInput || !selectKendaraanUser) return;

    const tglBerangkat = tanggalBerangkatInput.value;
    const tglKembali = tanggalKembaliInput.value;

    if (!tglBerangkat || !tglKembali) {
        selectKendaraanUser.innerHTML = '<option value="">-- Pilih tanggal dulu --</option>';
        return;
    }

    fetch(`{{ route('dashboard.user.kendaraan.tersedia') }}?tanggal_berangkat=${tglBerangkat}&tanggal_kembali=${tglKembali}`)
        .then(res => res.json())
        .then(data => {
            console.log('DATA KENDARAAN:', data);
            semuaKendaraanUser = data;
            renderDropdownKendaraanUser();
        })
        .catch(() => {
            selectKendaraanUser.innerHTML = '<option value="">Gagal load kendaraan</option>';
        });
}

function renderDropdownKendaraanUser() {
    const selectedIds = pilihanKendaraanUser.map(k => String(k.id));
    selectKendaraanUser.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';

    semuaKendaraanUser.forEach(k => {
        const sudahDipilih = selectedIds.includes(String(k.id_kendaraan));
        const option = document.createElement('option');
        option.value = k.id_kendaraan;
        option.dataset.nama = k.nama_kendaraan;
        option.dataset.kapasitas = k.kapasitas;
        option.disabled = k.dipakai || sudahDipilih;
        option.textContent = `${k.nama_kendaraan} — Kapasitas ${k.kapasitas} orang`
            + (k.dipakai ? ' (Tidak tersedia)' : '')
            + (sudahDipilih ? ' (Dipilih)' : '');
        selectKendaraanUser.appendChild(option);
    });
}

function tambahKendaraanUser() {
    const select = document.getElementById('selectKendaraanUser');
    const id = select.value;
    if (!id) { alert('Pilih kendaraan dulu.'); return; }
    if (pilihanKendaraanUser.find(k => k.id == id)) { alert('Kendaraan sudah dipilih.'); return; }

    const opt = select.options[select.selectedIndex];
    pilihanKendaraanUser.push({
        id,
        nama: opt.dataset.nama,
        kapasitas: parseInt(opt.dataset.kapasitas),
    });

    renderPilihanKendaraanUser();
    renderDropdownKendaraanUser();
    select.value = '';
}

function hapusKendaraanUser(id) {
    pilihanKendaraanUser = pilihanKendaraanUser.filter(k => k.id != id);
    renderPilihanKendaraanUser();
    renderDropdownKendaraanUser();
}

function renderPilihanKendaraanUser() {
    const container = document.getElementById('selectedKendaraanUser');
    const totalEl = document.getElementById('totalKapasitasUser');
    const hiddenEl = document.getElementById('kendaraanHiddenInputs');
    const warningEl = document.getElementById('warningKapasitas');
    const jumlahPeserta = parseInt(jumlahPesertaInput?.value || 0);

    container.innerHTML = '';
    hiddenEl.innerHTML = '';
    let total = 0;

    pilihanKendaraanUser.forEach(k => {
        total += k.kapasitas;

        // Card kendaraan dipilih
        const div = document.createElement('div');
        div.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; font-size:13px;';
        div.innerHTML = `
            <div>
                <strong style="color:#0369a1;">${k.nama}</strong>
                <span style="color:#6b7280; margin-left:8px;">Kapasitas ${k.kapasitas} orang</span>
            </div>
            <button type="button" onclick="hapusKendaraanUser('${k.id}')"
                style="background:#fee2e2; color:#dc2626; border:none; border-radius:6px; padding:4px 10px; font-size:12px; cursor:pointer;">
                Hapus
            </button>
        `;
        container.appendChild(div);

        // Hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_kendaraan[]';
        input.value = k.id;
        hiddenEl.appendChild(input);
    });

    totalEl.textContent = total;

    // Update info jumlah peserta
    const infoPeserta = document.getElementById('infoJumlahPeserta');
    if (infoPeserta) infoPeserta.textContent = jumlahPeserta || '-';

    // Warning kapasitas
    if (jumlahPeserta > 0 && pilihanKendaraanUser.length > 0) {
        warningEl.style.display = total < jumlahPeserta ? 'block' : 'none';
    } else {
        warningEl.style.display = 'none';
    }
}

if (tanggalBerangkatInput && tanggalKembaliInput) {
    tanggalBerangkatInput.addEventListener('change', loadKendaraanUser);
    tanggalKembaliInput.addEventListener('change', loadKendaraanUser);
}

// Update info peserta saat jumlah berubah
if (jumlahPesertaInput) {
    jumlahPesertaInput.addEventListener('input', function() {
        renderPilihanKendaraanUser();
        updateRingkasan();
    });
}

// auto load jika tanggal sudah ada (dari old() atau sudah diisi)
if (tanggalBerangkatInput && tanggalKembaliInput) {
    tanggalBerangkatInput.addEventListener('change', loadKendaraanUser);
    tanggalKembaliInput.addEventListener('change', loadKendaraanUser);

    // Auto load jika kedua tanggal sudah terisi (dari old())
    if (tanggalBerangkatInput.value && tanggalKembaliInput.value) {
        loadKendaraanUser();
    }
}

window.tambahKendaraanUser = tambahKendaraanUser;
window.hapusKendaraanUser = hapusKendaraanUser;

});
document.addEventListener('DOMContentLoaded', function () {
    const countdownEl = document.getElementById('countdownPembayaran');

    if (countdownEl) {
        const expiredAt = new Date("{{ !empty($bookingData['expired_at']) ? \Carbon\Carbon::parse($bookingData['expired_at'])->format('Y-m-d H:i:s') : '' }}").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiredAt - now;

            if (distance <= 0) {
                countdownEl.innerText = 'Kadaluarsa';
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.innerText =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
});
</script>
</main>
@endsection