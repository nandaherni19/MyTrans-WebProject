@extends('layouts.user')

@section('title', 'Detail Pesanan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/detail-pesanan.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')

<div class="page">

  <div class="header-card">
    <div>
      <h2>Detail Pemesanan</h2>
      <p class="kode">Kode Booking: BK001</p>
    </div>
    <div class="badge">✔ DIKONFIRMASI</div>
  </div>

  <div class="card">
    <h3>Informasi Pemesan</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Nama Lengkap</p>
        <p class="value">Amalia Khoirun Nisa</p>
      </div>
      <div>
        <p class="label">Email</p>
        <p class="value">amalia@gmail.com</p>
      </div>
      <div>
        <p class="label">No. Telepon</p>
        <p class="value">08123456789</p>
      </div>
      <div>
        <p class="label">Total Peserta</p>
        <p class="value">2 Orang</p>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Detail Perjalanan</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Destinasi</p>
        <p class="value">Pantai Watu Karung</p>
      </div>
      <div>
        <p class="label">Trayek</p>
        <p class="value">Pacitan, Jawa Timur</p>
      </div>
      <div>
        <p class="label">Tanggal Berangkat</p>
        <p class="value">17 Maret 2026</p>
      </div>
      <div>
        <p class="label">Tanggal Kembali</p>
        <p class="value">18 Maret 2026</p>
      </div>
    </div>

    <div class="duration">
      <span>Durasi Perjalanan</span>
      <strong>2 Hari 1 Malam</strong>
    </div>
  </div>

  <div class="card payment">
    <h3>Informasi Pembayaran</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Metode Pembayaran</p>
        <p class="value">QRIS</p>
      </div>
      <div>
        <p class="label">Status</p>
        <span class="status-success">● Lunas</span>
      </div>
      <div>
        <p class="label">Tanggal Pembayaran</p>
        <p class="value">10 Maret 2026</p>
      </div>
      <div>
        <p class="label">ID Transaksi</p>
        <p class="value">TRX001</p>
      </div>
    </div>

    <div class="price-box">
      <div><span>Paket Wisata</span><span>Rp 1.500.000</span></div>
      
      <div><span>Transportasi</span><span>Rp 300.000</span></div>
    </div>

    <div class="total">
      <p class="Total Pembayaran">Total Pembayaran</p>
      <strong>Rp 2.300.000</strong>
    </div>
  </div>

    <button onclick="downloadPDF()" class="btn-primary" style="margin-left:auto;">
        <i class="fa-solid fa-download"></i> Download
    </button>
</div>

</div>

@endsection


@push('scripts')


@endpush
