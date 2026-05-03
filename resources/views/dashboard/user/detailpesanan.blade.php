@extends('layouts.user')

  @section('title', 'Detail Pesanan')

    @push('styles')
      <link rel="stylesheet" href="{{ asset('css/user/detail-pesanan.css') }}">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @endpush

    @section('content')

      @php
        $statusRaw = $jenis === 'booking' ? ($data->status_booking ?? '-') : ($data->status_request ?? '-');

        if (in_array($statusRaw, ['pending', 'menunggu'])) {
        $statusLabel = 'MENUNGGU';
        $badgeClass = 'badge';
        $paymentStatusClass = 'status-warning';
        $paymentStatusText = '● Menunggu';
    } elseif (in_array($statusRaw, ['confirmed', 'dikonfirmasi'])) {
        $statusLabel = 'DIKONFIRMASI';
        $badgeClass = 'badge';
        $paymentStatusClass = 'status-info';
        $paymentStatusText = '● Dikonfirmasi';
    } elseif (in_array($statusRaw, ['selesai', 'done', 'completed'])) {
        $statusLabel = 'SELESAI';
        $badgeClass = 'badge';
        $paymentStatusClass = 'status-success';
        $paymentStatusText = '● Selesai';
    } else {
        $statusLabel = strtoupper($statusRaw);
        $badgeClass = 'badge';
        $paymentStatusClass = 'status-info';
        $paymentStatusText = '● ' . ucfirst($statusRaw);
    }

    $kodeBooking = $jenis === 'booking'
        ? 'BK' . str_pad($data->id_booking, 3, '0', STR_PAD_LEFT)
        : 'RW' . str_pad($data->id_request, 3, '0', STR_PAD_LEFT);

    $tanggalBerangkat = $jenis === 'booking'
        ? $data->tanggal_berangkat
        : $data->tanggal_keberangkatan;

    $tanggalKembali = $jenis === 'booking'
        ? $data->tanggal_kembali
        : ($data->tanggal_kembali ?? null);

    $destinasi = $jenis === 'booking'
        ? ($data->paket->nama_paket ?? '-')
        : 'Request Wisata Custom';

    $lokasi = '-';

    if ($jenis === 'booking' && $data->paket && $data->paket->kota) {
        $lokasi = $data->paket->kota->nama_kota;

        if ($data->paket->kota->provinsi) {
            $lokasi .= ', ' . $data->paket->kota->provinsi->nama_provinsi;
        }
    }

    $durasi = '-';
    $sudahBayar = 0;
    $sisaBayar = 0;
    $metodePembayaran = '-';
    $kodePembayaran = '-';
    $tanggalPembayaran = '-';
    $statusPembayaran = '-';

    if ($jenis === 'booking') {
        $sudahBayar = $data->pembayarans
            ->where('transaction_status', 'berhasil')
            ->sum('jumlah_bayar');

        $sisaBayar = max(($data->total_biaya ?? 0) - $sudahBayar, 0);

        $metodePembayaran = $data->pembayaranTerakhir->metode_pembayaran ?? '-';
        $kodePembayaran = $data->pembayaranTerakhir->kode_pembayaran ?? '-';
        $tanggalPembayaran = $data->pembayaranTerakhir && $data->pembayaranTerakhir->tanggal_bayar
            ? \Carbon\Carbon::parse($data->pembayaranTerakhir->tanggal_bayar)->translatedFormat('j F Y H:i')
            : '-';
        $statusPembayaran = $data->pembayaranTerakhir->transaction_status ?? '-';
    }
    if ($tanggalBerangkat && $tanggalKembali) {
        $start = \Carbon\Carbon::parse($tanggalBerangkat);
        $end = \Carbon\Carbon::parse($tanggalKembali);
        $hari = $start->diffInDays($end) + 1;
        $malam = max($hari - 1, 0);
        $durasi = $hari . ' Hari ' . $malam . ' Malam';
    } elseif ($jenis === 'request') {
        $durasi = $data->durasi ?? '-';
    }
@endphp

<div class="page">

  <div class="header-card">
    <div>
      <h2>Detail Pemesanan</h2>
      <p class="kode">Kode Booking: {{ $kodeBooking }}</p>
    </div>
    <div class="{{ $badgeClass }}">✔ {{ $statusLabel }}</div>
  </div>

  <div class="card">
    <h3>Informasi Pemesan</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Nama Lengkap</p>
        <p class="value">{{ auth()->user()->nama ?? '-' }}</p>
      </div>
      <div>
        <p class="label">Email</p>
        <p class="value">{{ auth()->user()->email ?? '-' }}</p>
      </div>
      <div>
        <p class="label">No. Telepon</p>
        <p class="value">{{ auth()->user()->no_hp ?? '-' }}</p>
      </div>
      <div>
        <p class="label">Total Peserta</p>
        <p class="value">{{ $data->jumlah_peserta ?? 0 }} Orang</p>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>Detail Perjalanan</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Destinasi</p>
        <p class="value">{{ $destinasi }}</p>
      </div>
      <div>
        <p class="label">Lokasi</p>
        <p class="value">{{ $lokasi }}</p>
      </div>
      <div>
        <p class="label">Tanggal Berangkat</p>
        <p class="value">
          {{ $tanggalBerangkat ? \Carbon\Carbon::parse($tanggalBerangkat)->translatedFormat('j F Y') : '-' }}
        </p>
      </div>
      <div>
        <p class="label">Tanggal Kembali</p>
        <p class="value">
          {{ $tanggalKembali ? \Carbon\Carbon::parse($tanggalKembali)->translatedFormat('j F Y') : '-' }}
        </p>
      </div>
      @if($jenis === 'booking')
        <div>
          <p class="label">Area / Kota Dilayani</p>
          <p class="value">{{ $data->kotaLayanan->nama_kota ?? '-' }}</p>
        </div>

        @if($data->tipe_booking === 'open_trip')
          <div>
            <p class="label">Titik Jemput</p>
            <p class="value">{{ $data->titikJemput->nama ?? '-' }}</p>
          </div>
        @else
          <div>
            <p class="label">Alamat Jemput</p>
            <p class="value">{{ $data->alamat_jemput ?? '-' }}</p>
          </div>
        @endif
      @endif
    </div>

    <div class="duration">
      <span>Durasi Perjalanan</span>
      <strong>{{ $durasi }}</strong>
    </div>
  </div>

  <div class="card payment">
    <h3>Informasi Pembayaran</h3>
    <div class="divider"></div>

    <div class="grid-2">
      <div>
        <p class="label">Metode Pembayaran</p>
        <p class="value">{{ strtoupper($metodePembayaran) }}</p>
      </div>
      <div>
        <p class="label">Status</p>
        <span class="{{ $paymentStatusClass }}">
            ● {{ ucfirst($statusPembayaran) }}
        </span>
      </div>
      <div>
        <p class="label">Tanggal Pembayaran</p>
        <p class="value">{{ ($data->pembayaranTerakhir && $data->pembayaranTerakhir->tanggal_bayar)? \Carbon\Carbon::parse($data->pembayaranTerakhir->tanggal_bayar)->translatedFormat('j F Y'): '-' }}
        </p>
      </div>
      <div>
        <p class="label">ID Transaksi</p>
        <p class="value">{{ $jenis === 'booking'? ($data->pembayaranTerakhir->kode_pembayaran ?? '-'): '-' }}
        </p>
      </div>
    </div>

    <div class="price-box">
      <div>
        <span>Total Harga</span>
        <span>Rp {{ number_format($data->total_biaya ?? 0, 0, ',', '.') }}</span>
      </div>

      <div>
        <span>Sudah Dibayar</span>
        <span>Rp {{ number_format($sudahBayar, 0, ',', '.') }}</span>
      </div>

      <div>
        <span>Sisa Pembayaran</span>
        <span>Rp {{ number_format($sisaBayar, 0, ',', '.') }}</span>
      </div>
      @if($data->opsi_pembayaran === 'lunas' && $sisaBayar > 0)
        <div>
          <small style="color:#f59e0b;">
            Menunggu pembayaran penuh
          </small>
        </div>
      @endif
    </div>

    <div class="total">
      <p>Total Pembayaran</p> 
        <strong>Rp {{ number_format($sudahBayar, 0, ',', '.') }}</strong>
    </div>
  </div>

  <div class="actions">
      {{-- Tombol lanjut QRIS --}}
      @if(
          $jenis === 'booking' &&
          $data->tipe_pembayaran === 'qris' &&
          ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'pending'
      )
          <a href="{{ route('dashboard.user.booking.qris', $data->id_booking) }}"
            class="btn-primary">
              Lanjutkan Pembayaran QRIS
          </a>
      @endif

      {{-- Tombol konfirmasi cash --}}
      @if(
          $jenis === 'booking' &&
          $data->tipe_pembayaran === 'cash' &&
          ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'pending'
      )
          @php
              $pesanCash = 'Halo Admin, saya ingin KONFIRMASI pembayaran CASH.' . "\n\n"
                  . 'ID Booking: ' . $kodeBooking . "\n"
                  . 'Nama: ' . (auth()->user()->nama ?? '-') . "\n"
                  . 'No. Telepon: ' . (auth()->user()->no_hp ?? '-') . "\n"
                  . 'Paket: ' . $destinasi . "\n"
                  . 'Tanggal Berangkat: ' . ($tanggalBerangkat ? \Carbon\Carbon::parse($tanggalBerangkat)->translatedFormat('j F Y') : '-') . "\n"
                  . 'Total Biaya: Rp ' . number_format($data->total_biaya ?? 0, 0, ',', '.') . "\n"
                  . 'Opsi Pembayaran: ' . ($data->opsi_pembayaran === 'dp' ? 'DP 25%' : 'Lunas') . "\n\n"
                  . 'Saya ingin melakukan pembayaran cash, mohon instruksinya.';
          @endphp

          <a href="https://wa.me/6285664837559?text={{ urlencode($pesanCash) }}"
            target="_blank"
            class="btn-primary btn-whatsapp">
              <i class="fa-brands fa-whatsapp"></i> Konfirmasi Pembayaran Cash
          </a>
      @endif

      {{-- Tombol pelunasan --}}
      @if(
          $jenis === 'booking' &&
          $data->opsi_pembayaran === 'dp' &&
          $data->status_booking === 'aktif' &&
          $sisaBayar > 0
      )
          <a href="{{ route('dashboard.user.booking.pelunasan', $data->id_booking) }}"
            class="btn-primary">
              Bayar Pelunasan
          </a>
      @endif

      <button onclick="downloadPDF()" class="btn-outline">
          <i class="fa-solid fa-download"></i> Download
      </button>

      {{-- Tombol Ajukan Pembatalan --}}
      @if($jenis === 'booking' && !in_array($statusRaw, ['selesai', 'batal']))
          @php
              $sudahLunas = $data->pembayarans
                  ->where('transaction_status', 'berhasil')
                  ->sum('jumlah_bayar');

              $infoRefund = '';
              if ($data->opsi_pembayaran === 'lunas' && $sudahLunas > 0) {
                  $refund = $sudahLunas * 0.85;
                  $infoRefund = 'Refund yang akan dikembalikan: Rp ' . number_format($refund, 0, ',', '.') . ' (85% dari pembayaran).';
              } else {
                  $infoRefund = 'Pembayaran DP tidak dapat dikembalikan.';
              }

              $pesanBatal = 'Halo Admin, saya ingin mengajukan PEMBATALAN booking.' . "\n\n"
                  . 'ID Booking: ' . $kodeBooking . "\n"
                  . 'Nama: ' . (auth()->user()->nama ?? '-') . "\n"
                  . 'No. Telepon: ' . (auth()->user()->no_hp ?? '-') . "\n"
                  . 'Paket: ' . $destinasi . "\n"
                  . 'Tanggal Berangkat: ' . ($tanggalBerangkat ? \Carbon\Carbon::parse($tanggalBerangkat)->translatedFormat('j F Y') : '-') . "\n"
                  . 'Total Biaya: Rp ' . number_format($data->total_biaya ?? 0, 0, ',', '.') . "\n"
                  . 'Opsi Pembayaran: ' . ($data->opsi_pembayaran === 'dp' ? 'DP 25%' : 'Lunas') . "\n\n"
                  . $infoRefund . "\n\n"
                  . 'Mohon konfirmasi pembatalan ini. Terima kasih.';
          @endphp

          <a href="https://wa.me/6285664837559?text={{ urlencode($pesanBatal) }}"
            target="_blank"
            class="btn-primary btn-cancel"
            onclick="return confirm('Yakin ingin mengajukan pembatalan? Perhatikan kebijakan refund yang berlaku.')">
              <i class="fa-brands fa-whatsapp"></i> Ajukan Pembatalan via WhatsApp
          </a>
      @endif

  </div>

  @if(
      $jenis === 'booking' &&
      $data->tipe_pembayaran === 'cash' &&
      ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'pending'
  )
      <small class="payment-note">
          Silakan hubungi admin untuk konfirmasi pembayaran cash.
      </small>
  @endif

</div>

@endsection

@push('scripts')
<script>
function downloadPDF() {
    window.print();
}
</script>
@endpush