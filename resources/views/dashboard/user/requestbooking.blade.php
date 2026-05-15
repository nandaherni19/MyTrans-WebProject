@extends('layouts.user')

@section('title', 'Request Booking Custom')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/requestbooking.css') }}">
@endpush

@section('content')
    <section class="request-container">
        <div class="request-card">
            <h2>Request Booking Custom</h2>
            <p class="subtitle">
                Buat permintaan paket wisata sesuai keinginan Anda. Tim kami akan menghubungi Anda melalui WhatsApp.
            </p>

            <form id="waForm">
                <div class="form-group">
                    <label>Tujuan Wisata *</label>
                    <input type="text" id="tujuan" placeholder="Contoh: Bali, Raja Ampat, Lombok" required>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Tanggal Keberangkatan *</label>
                        <input type="date" id="tanggal_keberangkatan" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Kembali *</label>
                        <input type="date" id="tanggal_kembali" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jumlah Peserta *</label>
                    <input type="number" id="jumlah_peserta" min="1" value="1" required>
                </div>

                <div class="form-group">
                    <label>Budget Per Orang</label>
                    <input type="number" id="budget" placeholder="Contoh: 1500000">
                </div>

                <small class="note">Estimasi budget per orang dalam Rupiah</small>

                <div class="form-group">
                    <label>Keterangan Tambahan *</label>
                    <textarea id="keterangan" rows="4"
                        placeholder="Ceritakan keinginan trip Anda, kebutuhan khusus, preferensi akomodasi, dll"
                        required></textarea>
                </div>

                <div class="info-box">
                    <strong>Cara Kerja Request Booking:</strong>
                    <ol>
                        <li>Isi form request booking</li>
                        <li>Sistem membuka WhatsApp otomatis</li>
                        <li>Tim kami akan merespon via WhatsApp</li>
                        <li>Admin input data ke sistem jika sudah deal</li>
                        <li>Anda bisa lanjut booking</li>
                    </ol>
                </div>

                <button type="button" class="btn-submit" onclick="kirimWA()">
                    Kirim Request via WhatsApp
                </button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function kirimWA() {
            const tujuan = document.getElementById('tujuan').value;
            const berangkat = document.getElementById('tanggal_keberangkatan').value;
            const kembali = document.getElementById('tanggal_kembali').value;
            const peserta = document.getElementById('jumlah_peserta').value;
            const budget = document.getElementById('budget').value;
            const keterangan = document.getElementById('keterangan').value;

            // Validasi
            if (!tujuan || !berangkat || !kembali || !peserta || !keterangan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap',
                    text: 'Mohon lengkapi semua field wajib.',
                    confirmButtonColor: '#16a34a'
                });
                return;
            }

            let pesan = `Halo Admin MyTrans Nusa Pariwisata,\n\n`;
            pesan += `Saya ingin request paket wisata custom:\n`;
            pesan += `Tujuan: ${tujuan}\n`;
            pesan += `Tanggal Berangkat: ${berangkat}\n`;
            pesan += `Tanggal Kembali: ${kembali}\n`;
            pesan += `Jumlah Peserta: ${peserta} orang\n`;

            if (budget) {
                pesan += `Budget: Rp ${budget}/orang\n`;
            }

            pesan += `\nKeterangan Tambahan:\n${keterangan}`;

            const noAdmin = '6285664837559';
            const url = `https://wa.me/${noAdmin}?text=${encodeURIComponent(pesan)}`;

            // Popup sukses
            Swal.fire({
                icon: 'success',
                title: 'Request Berhasil Dikirim!',
                text: 'Anda akan diarahkan ke WhatsApp Admin.',
                confirmButtonText: 'Lanjut ke WhatsApp',
                confirmButtonColor: '#16a34a'
            }).then((result) => {
                if (result.isConfirmed) {
                    // buka whatsapp
                    window.open(url, '_blank');

                    // reset form
                    document.getElementById('waForm').reset();

                    // redirect halaman setelah 1 detik
                    setTimeout(() => {
                        window.location.href = '/dashboard/user';
                    }, 1000);
                }
            });
        }
    </script>
@endpush