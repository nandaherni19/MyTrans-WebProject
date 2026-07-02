@extends('layouts.user')

@section('title', 'Detail Pesanan')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/user/detail-pesanan.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

  @php
    $statusRaw = $data->status_booking ?? '-';

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

    } elseif (in_array($statusRaw, ['batal', 'dibatalkan', 'cancel'])) {
      $statusLabel = 'DIBATALKAN';
      $badgeClass = 'badge badge-danger';
      $paymentStatusClass = 'status-danger';
      $paymentStatusText = '● Dibatalkan';

    } else {
      $statusLabel = strtoupper($statusRaw);
      $badgeClass = 'badge';
      $paymentStatusClass = 'status-info';

      $paymentStatusText = '● ' . ucfirst($statusRaw);
    }

    $kodeBooking = 'BK' . str_pad($data->id_booking, 3, '0', STR_PAD_LEFT);

    $tanggalBerangkat = $data->tanggal_berangkat;

    $tanggalKembali = $data->tanggal_kembali ?? null;

    $destinasi = $data->paket->nama_paket ?? '-';

    $lokasi = '-';

    if ($data->paket && $data->paket->kota) {
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

    if ($tanggalBerangkat && $tanggalKembali) {
      $start = \Carbon\Carbon::parse($tanggalBerangkat);
      $end = \Carbon\Carbon::parse($tanggalKembali);
      $hari = $start->diffInDays($end) + 1;
      $malam = max($hari - 1, 0);
      $durasi = $hari . ' Hari ' . $malam . ' Malam';
    }
  @endphp

  {{-- Informasi Refund --}}
  @php
    $refundData = $data->pembayarans
      ->where('status_refund', 'selesai')
      ->first();

    $jumlahRefund = 0;

    // total pembayaran berhasil
    $totalBerhasil = $data->pembayarans
      ->where('transaction_status', 'berhasil')
      ->sum('jumlah_bayar');

    // refund hanya jika pembayaran sudah lunas
    if ($refundData && $totalBerhasil >= ($data->total_biaya ?? 0)) {
      $jumlahRefund = $totalBerhasil * 0.85;
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

    @if(in_array($statusRaw, ['batal', 'dibatalkan', 'cancel']))
      <div class="card" style="border-left:4px solid #ef4444;">
        <h3 style="color:#dc2626;">
          <i class="fa-solid fa-circle-xmark"></i>
          Pesanan Dibatalkan
        </h3>

        <p style="margin-top:10px; color:#64748b; font-size:14px;">
          Pesanan ini telah dibatalkan.
        </p>

        @if($refundData && $jumlahRefund > 0)
          <p style="margin-top:8px; color:#16a34a; font-weight:600;">
            Refund berhasil diproses sebesar
            Rp {{ number_format($jumlahRefund, 0, ',', '.') }}
          </p>
        @endif
      </div>
    @endif

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
        <div>
          <p class="label">Area / Kota Dilayani</p>
          <p class="value">{{ $data->kotaLayanan->nama_kota ?? '-' }}</p>
        </div>

        @if($data->tipe_booking === 'open_trip')
          <div>
            <p class="label">Alamat Jemput</p>
            <p class="value">{{ $data->alamat_jemput ?? '-' }}</p>
          </div>
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

          @if(in_array($statusRaw, ['batal', 'dibatalkan', 'cancel']))
            <span class="status-danger">
              ● Dibatalkan
            </span>
          @else
            <span class="{{ $paymentStatusClass }}">
              ● {{ ucfirst($statusPembayaran) }}
            </span>
          @endif
        </div>
        <div>
          <p class="label">Tanggal Pembayaran</p>
          <p class="value">
            {{ ($data->pembayaranTerakhir && $data->pembayaranTerakhir->tanggal_bayar) ? \Carbon\Carbon::parse($data->pembayaranTerakhir->tanggal_bayar)->translatedFormat('j F Y') : '-' }}
          </p>
        </div>
        <div>
          <p class="label">ID Transaksi</p>
          <p class="value">{{ $data->pembayaranTerakhir->kode_pembayaran ?? '-' }}
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

      @if($refundData && $jumlahRefund > 0)
        <div class="refund-box">

          <div class="refund-row">
            <span>Status Refund</span>
            <strong class="refund-success">
              Refund Berhasil
            </strong>
          </div>

          <div class="refund-row">
            <span>Jumlah Refund</span>
            <strong class="refund-amount">
              Rp {{ number_format($jumlahRefund, 0, ',', '.') }}
            </strong>
          </div>

          <div class="refund-row">
            <span>Keterangan</span>
            <span>
              Dana telah dikembalikan ke pelanggan
            </span>
          </div>

        </div>
      @endif

      {{-- Catatan cash --}}
      @if(

          $data->tipe_pembayaran === 'cash' &&
          ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'pending'
        )
        <small class="payment-note">
          Silakan hubungi admin untuk konfirmasi pembayaran cash.
        </small>
      @endif

    </div>

    <div class="actions">
      {{-- Tombol lanjut QRIS --}}
      @if(
          !in_array($statusRaw, ['batal', 'dibatalkan', 'cancel']) &&
          $data->tipe_pembayaran === 'qris' &&
          ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'pending'
        )
        <a href="{{ route('dashboard.user.booking.qris', $data->id_booking) }}" class="btn-primary">
          Lanjutkan Pembayaran QRIS
        </a>
      @endif

      {{-- Tombol konfirmasi cash --}}
      @if(

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

        <a href="https://wa.me/6282140360481?text={{ urlencode($pesanCash) }}" target="_blank"
          class="btn-primary btn-whatsapp">
          <i class="fa-brands fa-whatsapp"></i> Konfirmasi Pembayaran Cash
        </a>
      @endif

      {{-- Tombol pelunasan --}}
      @if(

          $data->opsi_pembayaran === 'dp' &&
          $data->status_booking === 'aktif' &&
          $sisaBayar > 0 &&
          ($data->pembayaranTerakhir->transaction_status ?? 'pending') === 'berhasil'
        )
        <a href="{{ route('dashboard.user.booking.pelunasan', $data->id_booking) }}" class="btn-primary">
          Bayar Pelunasan
        </a>
      @endif

      {{-- Tombol Ajukan Pembatalan --}}
      @if(!in_array($statusRaw, ['selesai', 'batal']))
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

        <a href="https://wa.me/6282140360481?text={{ urlencode($pesanBatal) }}" class="btn-primary btn-cancel"
          onclick="return confirmCancel(event, this)">
          <i class="fa-brands fa-whatsapp"></i> Ajukan Pembatalan via WhatsApp
        </a>
      @endif

      <button onclick="downloadPDF()" class="btn-outline">
        <i class="fa-solid fa-download"></i> Download
      </button>

    </div>

  </div>

@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    function confirmCancel(event, el) {
      event.preventDefault();

      Swal.fire({
        title: 'Konfirmasi Pembatalan',
        text: 'Yakin ingin mengajukan pembatalan? Perhatikan kebijakan refund yang berlaku.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, lanjutkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          window.open(el.href, '_blank');
        }
      });

      return false;
    }
    
  function downloadPDF() {
    const { jsPDF } = window.jspdf;

    document.querySelector('.actions').style.display = 'none';

    html2canvas(document.querySelector('.page'), {
        scale: 2,
        useCORS: true,
        logging: false
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');

        const pageWidth = 210;   
        const pageHeight = 297;  
        const margin = 10;

        const imgWidth = pageWidth - (margin * 2);
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        
        if (imgHeight <= pageHeight - (margin * 2)) {
            pdf.addImage(imgData, 'PNG', margin, margin, imgWidth, imgHeight);
        } else {
          
            let yOffset = 0;
            const sliceHeight = pageHeight - (margin * 2);

            while (yOffset < imgHeight) {
                pdf.addImage(imgData, 'PNG', margin, margin - yOffset, imgWidth, imgHeight);
                yOffset += sliceHeight;
                if (yOffset < imgHeight) pdf.addPage();
            }
        }

        pdf.save('detail-pesanan.pdf');
        document.querySelector('.actions').style.display = 'flex';
    });
}
  </script>
@endpush