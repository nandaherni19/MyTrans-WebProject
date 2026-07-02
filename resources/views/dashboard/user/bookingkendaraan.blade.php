@extends('layouts.user')

@section('title', 'Booking Kendaraan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/bookingkendaraan.css') }}">
@endpush

@section('content')

<div class="booking-container">

    {{-- FORM BOOKING --}}
    <div class="booking-form-card">

        <div class="form-header">
            <h2>Form Booking</h2>
            <p>
                Lengkapi detail perjalanan Anda untuk mendapatkan
                pengalaman terbaik bersama MyTrans.
            </p>
        </div>

        <form>

            <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">

            <div class="form-grid">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="whatsapp" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Berangkat</label>
                    <input type="date" id="tanggalMulai" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Pulang</label>
                    <input type="date" id="tanggalSelesai" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Peserta</label>
                    <input type="number" 
                    id="jumlahPeserta" 
                    name="jumlah_peserta" 
                    value="{{ $jumlahPeserta ?? '' }}" min="1" required>
                </div>

                <div class="form-group">
                    <label>Tujuan Wisata</label>
                    <input type="text" name="tujuan" required>
                </div>

            </div>

            <div class="form-group full">
                <label>Daerah Penjemputan</label>
                <select name="pickup" required>
                    <option value="">-- Pilih Daerah Penjemputan --</option>
                    <option value="Kota Madiun">Kota Madiun</option>
                    <option value="Kabupaten Madiun">Kabupaten Madiun</option>
                    <option value="Magetan">Magetan</option>
                    <option value="Ngawi">Ngawi</option>
                    <option value="Ponorogo">Ponorogo</option>
                    <option value="Pacitan">Pacitan</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Alamat Detail Penjemputan</label>
                <textarea
                    rows="3"
                    name="alamat_jemput"
                    placeholder="Contoh: Jl. Pahlawan No. 10, Kel. Manguharjo, Kota Madiun"
                    required></textarea>
            </div>

            <div class="form-group full">
                <label>Catatan Tambahan</label>
                <textarea
                    rows="5"
                    name="catatan"
                    placeholder="Permintaan khusus, jam keberangkatan, dll"></textarea>
            </div>

        </form>

    </div>

    {{-- SIDEBAR --}}
    <div class="booking-sidebar">

        <div class="summary-card">

            <img
                src="{{ asset('storage/'.$kendaraan->foto_kendaraan) }}"
                class="vehicle-image">

            <h3>{{ $kendaraan->nama_kendaraan }}</h3>

            <div class="summary-item">
                <span>Harga / Hari</span>
                <strong>
                    Rp {{ number_format($kendaraan->harga_sewa,0,',','.') }}
                </strong>
            </div>

            <div class="summary-item">
                <span>Kapasitas Kendaraan</span>
                <strong>{{ $kendaraan->kapasitas }} Orang</strong>
            </div>

            <div class="summary-item">
                <span>Durasi</span>
                <strong id="durasiHari">0 Hari</strong>
            </div>

            <div class="summary-item">
                <span>Jumlah Peserta</span>
                <strong id="pesertaText">0 Orang</strong>
            </div>

            <div class="summary-item">
                <span>Estimasi Armada</span>
                <strong id="armadaText">1 Unit</strong>
            </div>

            <div class="summary-item">
                <span>Biaya Layanan</span>
                <strong>Rp 50.000</strong>
            </div>

            <hr>

            <div class="total-price">
                <span>Total Estimasi</span>
                <h2 id="totalHarga">Rp 0</h2>
            </div>

            <div class="booking-note">
                <p>
                    *Harga sudah termasuk kendaraan dan sopir.<br>
                    *Jumlah armada yang dibutuhkan akan disesuaikan dengan jumlah peserta dan ketersediaan kendaraan.<br>
                    *Biaya tambahan mungkin dikenakan sesuai tujuan wisata, jarak tempuh,
                    biaya tol, parkir, dan kebutuhan perjalanan lainnya.<br>
                    *Konfirmasi harga final akan disampaikan oleh admin setelah pemesanan diterima.
                </p>
            </div>

            <button type="button" onclick="kirimWhatsApp()" class="btn-booking">
                Kirim Booking
            </button>

        </div>

    </div>

</div>

@push('scripts')
<script>
(function () {
    const hargaPerHari = {{ $kendaraan->harga_sewa }};
    const kapasitas    = {{ $kendaraan->kapasitas }};

    const mulaiInput   = document.getElementById('tanggalMulai');
    const selesaiInput = document.getElementById('tanggalSelesai');
    const pesertaInput = document.getElementById('jumlahPeserta');

    function formatRp(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function update() {
        const mulai   = new Date(mulaiInput.value);
        const selesai = new Date(selesaiInput.value);
        const peserta = parseInt(pesertaInput.value) || 0;

        let hari = 0;

        if (
            mulaiInput.value &&
            selesaiInput.value &&
            selesai > mulai
        ) {
            hari = Math.ceil(
                (selesai - mulai) / (1000 * 60 * 60 * 24)
            );
        }

        const biayaLayanan = 50000;

        const armada =
            peserta > 0
                ? Math.ceil(peserta / kapasitas)
                : 1;

        const total =
            (hargaPerHari * hari * armada) +
            biayaLayanan;

        document.getElementById('durasiHari').textContent =
            hari + ' Hari';

        document.getElementById('pesertaText').textContent =
            peserta + ' Orang';

        document.getElementById('armadaText').textContent =
            armada + ' Unit';

        document.getElementById('totalHarga').textContent =
            formatRp(total);
    }

    mulaiInput.addEventListener('change', update);
    selesaiInput.addEventListener('change', update);
    pesertaInput.addEventListener('input', update);

    // Jalankan otomatis saat halaman load karena data sudah pre-filled
    update();
    })();

    function kirimWhatsApp() {
        const nama     = document.querySelector('input[name="nama"]').value;
        const whatsapp = document.querySelector('input[name="whatsapp"]').value;
        const mulai    = document.getElementById('tanggalMulai').value;
        const selesai  = document.getElementById('tanggalSelesai').value;
        const peserta  = document.getElementById('jumlahPeserta').value;
        const tujuan   = document.querySelector('input[name="tujuan"]').value;
        const pickup = document.querySelector('select[name="pickup"]').value;
        const alamatJemput = document.querySelector('textarea[name="alamat_jemput"]').value;
        const catatan  = document.querySelector('textarea[name="catatan"]').value;
        const kapasitas = {{ $kendaraan->kapasitas }};
        const armada =
            peserta && kapasitas
                ? Math.ceil(parseInt(peserta) / kapasitas)
                : 1;

        if (
            !nama ||
            !whatsapp ||
            !mulai ||
            !selesai ||
            !peserta ||
            !tujuan ||
            !pickup ||
            !alamatJemput
        ) {
            alert('Harap lengkapi semua field yang wajib diisi!');
            return;
        }

        const tglMulai = new Date(mulai);
        const tglSelesai = new Date(selesai);

        if (tglSelesai <= tglMulai) {
            alert('Tanggal pulang harus setelah tanggal berangkat.');
            return;
        }

        const pesan =
            `Halo Admin MyTrans, saya ingin booking kendaraan:

            Kendaraan: {{ $kendaraan->nama_kendaraan }}
            Kapasitas Kendaraan: ${kapasitas} Orang

            Nama: ${nama}
            WhatsApp: ${whatsapp}

            Tanggal Berangkat: ${mulai}
            Tanggal Pulang: ${selesai}

            Jumlah Peserta: ${peserta} Orang
            Estimasi Armada: ${armada} Unit

            Tujuan Wisata: ${tujuan}

            Daerah Penjemputan: ${pickup}
            Alamat Penjemputan: ${alamatJemput}

            Catatan Tambahan:
            ${catatan}

            Mohon konfirmasi armada dan harga final.
            Terima kasih.`;

        const nomorAdmin = '6282140360481';  
        const url = `https://wa.me/${nomorAdmin}?text=${encodeURIComponent(pesan)}`;
        window.open(url, '_blank');
    }
</script>
@endpush

@endsection