@extends('layouts.user')

@section('title', 'Pelunasan Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/pelunasan.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
    <main class="pelunasan-page">
        <h1 class="page-title">Pelunasan Booking</h1>

        <div class="pelunasan-card">
            <h2>{{ $booking->paket->nama_paket ?? 'Booking Wisata' }}</h2>

            <div class="payment-summary">
                <div class="payment-row">
                    <span>Booking ID</span>
                    <strong>BK{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}</strong>
                </div>

                <div class="payment-row">
                    <span>Total Biaya</span>
                    <strong>Rp {{ number_format($booking->total_biaya, 0, ',', '.') }}</strong>
                </div>

                <div class="payment-row">
                    <span>Sudah Dibayar</span>
                    <strong>Rp {{ number_format($sudahBayar ?? 0, 0, ',', '.') }}</strong>
                </div>

                <div class="payment-row">
                    <span>Sisa Bayar</span>
                    <strong class="sisa-highlight">Rp {{ number_format($sisaBayar, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="actions">
                <button onclick="openQris()" class="btn-qris payment-choice" type="button" id="btnQris">
                    <i class="fa-solid fa-qrcode"></i>
                    Bayar via QRIS
                </button>

                <button onclick="openCash()" class="btn-whatsapp payment-choice" type="button" id="btnCash">
                    <i class="fa-brands fa-whatsapp"></i>
                    Bayar Cash
                </button>

                <a href="{{ route('dashboard.user.detailpesanan', $booking->id_booking) }}" class="btn-outline">
                    <i class="fa-solid fa-receipt"></i>
                    Detail Pesanan
                </a>
            </div>
        </div>

        <div id="qrisModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeQris()">&times;</span>
                <h3>Scan QRIS untuk Pelunasan</h3>

                <div id="qrisContainer">
                    <p>Loading QRIS...</p>
                </div>
            </div>
        </div>

        <div id="cashModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCash()">&times;</span>

                <div class="modal-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>

                <h3>Pembayaran Cash</h3>

                <p>
                    Silakan lakukan pembayaran ke kantor/admin.<br>
                    Booking akan dikonfirmasi setelah pembayaran diterima.
                </p>

                <div class="cash-info-box">
                    <div class="cash-info-item">
                        <div class="cash-info-icon">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div class="cash-info-text">
                            <strong>Pembayaran dilakukan di kantor/admin</strong>
                            <span>Datang langsung ke kantor kami untuk melakukan pembayaran.</span>
                        </div>
                    </div>

                    <div class="cash-info-item">
                        <div class="cash-info-icon">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="cash-info-text">
                            <strong>Konfirmasi setelah pembayaran diterima</strong>
                            <span>Booking Anda akan kami konfirmasi setelah pembayaran kami terima.</span>
                        </div>
                    </div>
                </div>

                @php
                    $waMessage = 'Halo admin, saya ingin konfirmasi pelunasan booking '
                        . 'BK' . str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT)
                        . ' atas nama ' . (auth()->user()->nama ?? '-')
                        . '. Sisa bayar: Rp ' . number_format($sisaBayar, 0, ',', '.');
                @endphp

                <a href="https://wa.me/6285664837559?text={{ urlencode($waMessage) }}" target="_blank" class="btn-primary"
                    style="margin-top:15px; display:inline-flex; text-decoration:none;">
                    <i class="fa-brands fa-whatsapp"></i>
                    Konfirmasi via WhatsApp
                </a>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        let interval;

        function startCheckStatus() {
            interval = setInterval(() => {
                fetch(`/booking/check-status/{{ $booking->id_booking }}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status_pembayaran === 'berhasil') {
                            clearInterval(interval);

                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran berhasil',
                                text: 'Booking Anda sudah terkonfirmasi'
                            }).then(() => {
                                window.location.href = "{{ route('dashboard.user.detailpesanan', $booking->id_booking) }}";
                            });
                        }
                    });
            }, 3000);
        }

        function openQris() {
            document.getElementById('btnQris').classList.add('active');
            document.getElementById('btnCash').classList.remove('active');

            document.getElementById('qrisModal').style.display = 'flex';

            fetch("{{ route('dashboard.user.booking.pelunasan.qris', $booking->id_booking) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.qr_url) {
                        document.getElementById('qrisContainer').innerHTML =
                            `<img src="${data.qr_url}" width="250">`;

                        // mulai cek status pembayaran
                        startCheckStatus();

                    } else {
                        document.getElementById('qrisContainer').innerHTML =
                            `<p>Gagal load QRIS</p>`;
                    }
                });
        }

        function closeQris() {
            document.getElementById('qrisModal').style.display = 'none';
        }

        function openCash() {
            document.getElementById('btnCash').classList.add('active');
            document.getElementById('btnQris').classList.remove('active');

            document.getElementById('cashModal').style.display = 'flex';
        }

        function closeCash() {
            document.getElementById('cashModal').style.display = 'none';
        }
    </script>
@endpush