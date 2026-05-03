@extends('layouts.admin')
@section('title', 'Kelola Paket Wisata dan Destinasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-paket-wisata.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="paket-topbar">
    <div class="paket-title">
                <h1>Kelola Paket Wisata</h1>
                <p>Kelola Paket Wisata dan Perbarui Data Paket Wisata</p>
            </div>

    <div class="header-actions">
        <button type="button" class="btn-primary" onclick="openTambah()">
            Tambah Paket Wisata
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div style="color:red; margin-bottom: 14px;">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="main-scroll">
    <section class="paket-wrapper">
        <div class="paket-grid">
            @forelse($pakets as $paket)
                <div class="paket-card">
                    <img src="{{ $paket->gambar ? asset('storage/'.$paket->gambar) : asset('img/default.png') }}" alt="paket">

                    <div class="paket-body">
                        <h3>{{ $paket->nama_paket }}</h3>

                        <p class="paket-status 
                            @if($paket->status_auto == 'aktif') status-aktif
                            @elseif($paket->status_auto == 'penuh') status-penuh
                            @else status-lewat
                            @endif
                        ">
                            @if($paket->status_auto == 'aktif')
                                ✅ Aktif
                            @elseif($paket->status_auto == 'penuh')
                                ⚠️ Penuh
                            @else
                                ❌ Lewat / Nonaktif
                            @endif
                        </p>

                        <p class="paket-location">
                            📍 {{ $paket->kota->nama_kota ?? '-' }}
                            @if($paket->kota && $paket->kota->provinsi)
                                , {{ $paket->kota->provinsi->nama_provinsi }}
                            @endif
                        </p>

                        <p class="paket-destination">
                            Tipe: {{ $paket->tipe === 'open_trip' ? 'Open Trip' : 'Paket Wisata' }}
                        </p>

                        @if($paket->tipe === 'open_trip')
                            <p class="paket-date">
                                📅 Keberangkatan:
                                {{ $paket->tanggal_berangkat ? \Carbon\Carbon::parse($paket->tanggal_berangkat)->format('d M Y') : '-' }}
                            </p>

                            <p class="paket-date">
                                📅 Kepulangan:
                                {{ $paket->tanggal_kembali ? \Carbon\Carbon::parse($paket->tanggal_kembali)->format('d M Y') : '-' }}
                            </p>

                            <p class="paket-capacity">
                                Kapasitas {{ $paket->kapasitas ?? 0 }} orang
                            </p>

                            <p class="paket-capacity">
                                Sisa kursi {{ $paket->sisa_kursi ?? 0 }} orang
                            </p>

                            @if($paket->titikJemput->isNotEmpty())
                                <p class="paket-capacity">
                                    Titik Jemput: {{ $paket->titikJemput->pluck('nama')->join(', ') }}
                                </p>
                            @endif

                        @else
                            <p class="paket-date">
                                📅 Tanggal: Request sesuai kebutuhan
                            </p>

                            <p class="paket-capacity">
                                Minimal peserta {{ $paket->min_peserta ?? '-' }} orang
                            </p>
                        @endif

                        @if($paket->kotaLayanan->isNotEmpty())
                            <p class="paket-capacity">
                                Kota Dilayani: {{ $paket->kotaLayanan->pluck('nama_kota')->join(', ') }}
                            </p>
                        @endif

                        <p class="paket-capacity">
                            Durasi: {{ $paket->durasi }} hari
                        </p>

                        <p class="paket-price-label">Harga Mulai Dari</p>
                        <h4 class="paket-price">Rp {{ number_format($paket->harga, 0, ',', '.') }}</h4>

                        <div class="paket-actions">
                            <button
                                type="button"
                                class="btn-edit"
                                onclick='openEdit(
                                    "{{ $paket->id_paket }}",
                                    @json($paket->nama_paket),
                                    @json($paket->tipe),
                                    "{{ $paket->id_kota }}",
                                    "{{ $paket->kapasitas }}",
                                    "{{ $paket->min_peserta }}",
                                    "{{ $paket->durasi }}",
                                    "{{ $paket->id_kendaraan }}",
                                    "{{ $paket->harga }}",
                                    @json($paket->fasilitas),
                                    @json($paket->deskripsi),
                                    @json($paket->status),
                                    "{{ $paket->tanggal_berangkat }}",
                                    "{{ $paket->tanggal_kembali }}",
                                    @json($paket->titikJemput->pluck("id_titik_jemput")),
                                    @json($paket->kotaLayanan->pluck("id_kota"))
                                )'
                            >
                                <i class="fa-solid fa-pen"></i>
                                <span>Edit</span>
                            </button>

                            <button
                                type="button"
                                class="btn-delete"
                                onclick="openHapus('{{ $paket->id_paket }}')"
                            >
                                <i class="fa-solid fa-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <h3>Belum ada data paket wisata</h3>
                </div>
            @endforelse
        </div>
    </section>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-overlay" id="modalTambah">
    <div class="tambah-modal">
        <h2 class="tambah-title">Tambah Paket Wisata</h2>

        <form action="{{ route('dashboard.superadmin.kelola-paket-wisata.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="tambah-grid">
                <div class="tambah-group">
                    <label>Tipe Paket <span>*</span></label>
                    <select name="tipe" id="tambahTipe" required>
                        <option value="open_trip">Open Trip</option>
                        <option value="paket">Paket Wisata</option>
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Kota Tujuan <span>*</span></label>
                    <select name="id_kota" id="tambahKota" class="select-kota-tambah" required>
                        <option value="">-- Pilih Kota --</option>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota->id_kota }}">
                                {{ $kota->nama_kota }}
                                @if($kota->provinsi)
                                    - {{ $kota->provinsi->nama_provinsi }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Nama paket wisata <span>*</span></label>
                    <input type="text" name="nama_paket" placeholder="Pantai Watu Karung" required>
                </div>

                <div class="tambah-group open-trip-field">
                    <label>Kapasitas</label>
                    <input type="number" id="tambahKapasitas" name="kapasitas" placeholder="50" min="1">
                </div>

                <div class="tambah-group open-trip-field">
                    <label>Tanggal Keberangkatan</label>
                    <input type="date" id="tambahTanggalBerangkat" name="tanggal_berangkat">
                </div>

                <div class="tambah-group open-trip-field">
                    <label>Tanggal Kepulangan</label>
                    <input type="date" id="tambahTanggalKembali" name="tanggal_kembali">
                </div>

                <div class="tambah-group">
                    <label>Durasi <span>*</span></label>
                    <input type="number" id="tambahDurasi" name="durasi" placeholder="2" min="1" required>
                </div>

                <div class="tambah-group paket-field">
                    <label>Minimal Peserta</label>
                    <input type="number" name="min_peserta" placeholder="4" min="1">
                </div>

                <div class="tambah-group open-trip-field">
                    <label>Kendaraan</label>
                    <select name="id_kendaraan" id="tambahKendaraan">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($kendaraans as $kendaraan)
                            <option value="{{ $kendaraan->id_kendaraan }}" data-kapasitas="{{ $kendaraan->kapasitas }}">
                                {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Titik Jemput (hanya open trip) --}}
                <div class="tambah-group open-trip-field">
                    <label>Titik Jemput</label>
                    <select name="titik_jemput[]" id="tambahTitikJemput" multiple>
                        @foreach($titikJemputs as $titik)
                            <option value="{{ $titik->id_titik_jemput }}">
                                {{ $titik->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kota Layanan --}}
                <div class="tambah-group">
                    <label>Kota yang Dilayani</label>
                    <select name="kota_layanan[]" id="tambahKotaLayanan" multiple>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota->id_kota }}">
                                {{ $kota->nama_kota }}
                                @if($kota->provinsi) - {{ $kota->provinsi->nama_provinsi }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Harga <span>*</span></label>
                    <input type="number" name="harga" placeholder="1700000" min="0" required>
                </div>

                <div class="tambah-group">
                    <label>Status <span>*</span></label>
                    <select name="status" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar">
                </div>
            </div>

            <h3 class="desc-title">Deskripsi Paket Wisata</h3>

            <div class="tambah-grid">
                <div class="tambah-group">
                    <label>Fasilitas yang didapatkan</label>
                    <textarea name="fasilitas" class="auto-textarea"></textarea>
                </div>

                <div class="tambah-group">
                    <label>Deskripsi Paket Wisata</label>
                    <textarea name="deskripsi" class="auto-textarea"></textarea>
                </div>
            </div>

            <div class="tambah-actions">
                <button type="button" class="btn-modal-secondary" onclick="closeTambah()">Kembali</button>
                <button type="submit" class="btn-modal-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEdit">
    <div class="tambah-modal">
        <h2 class="tambah-title">Edit Paket Wisata</h2>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="tambah-grid">
                <div class="tambah-group">
                    <label>Tipe Paket <span>*</span></label>
                    <select name="tipe" id="editTipe" required>
                        <option value="open_trip">Open Trip</option>
                        <option value="paket">Paket Wisata</option>
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Kota Tujuan <span>*</span></label>
                    <select id="editKota" name="id_kota" class="select-kota-edit" required>
                        <option value="">-- Pilih Kota --</option>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota->id_kota }}">
                                {{ $kota->nama_kota }}
                                @if($kota->provinsi)
                                    - {{ $kota->provinsi->nama_provinsi }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Nama paket wisata <span>*</span></label>
                    <input type="text" id="editNamaPaket" name="nama_paket" required>
                </div>

                <div class="tambah-group edit-open-trip-field">
                    <label>Tanggal Keberangkatan</label>
                    <input type="date" id="editTanggalBerangkat" name="tanggal_berangkat">
                </div>

                <div class="tambah-group edit-open-trip-field">
                    <label>Tanggal Kepulangan</label>
                    <input type="date" id="editTanggalKembali" name="tanggal_kembali">
                </div>

                <div class="tambah-group">
                    <label>Durasi <span>*</span></label>
                    <input type="number" id="editDurasi" name="durasi" min="1" required>
                </div>

                <div class="tambah-group edit-open-trip-field">
                    <label>Kapasitas</label>
                    <input type="number" id="editKapasitas" name="kapasitas" min="1">
                </div>

                <div class="tambah-group edit-paket-field">
                    <label>Minimal Peserta</label>
                    <input type="number" id="editMinPeserta" name="min_peserta" min="1">
                </div>

                <div class="tambah-group edit-open-trip-field">
                    <label>Kendaraan</label>
                    <select name="id_kendaraan" id="editKendaraan">
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($kendaraans as $kendaraan)
                            <option value="{{ $kendaraan->id_kendaraan }}" data-kapasitas="{{ $kendaraan->kapasitas }}">
                                {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Titik Jemput (hanya open trip) --}}
                <div class="tambah-group edit-open-trip-field">
                    <label>Titik Jemput</label>
                    <select name="titik_jemput[]" id="editTitikJemput" multiple>
                        @foreach($titikJemputs as $titik)
                            <option value="{{ $titik->id_titik_jemput }}">
                                {{ $titik->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kota Layanan --}}
                <div class="tambah-group">
                    <label>Kota yang Dilayani</label>
                    <select name="kota_layanan[]" id="editKotaLayanan" multiple>
                        @foreach($kotas as $kota)
                            <option value="{{ $kota->id_kota }}">
                                {{ $kota->nama_kota }}
                                @if($kota->provinsi) - {{ $kota->provinsi->nama_provinsi }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Harga <span>*</span></label>
                    <input type="number" id="editHarga" name="harga" min="0" required>
                </div>

                <div class="tambah-group">
                    <label>Status <span>*</span></label>
                    <select id="editStatus" name="status" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="tambah-group">
                    <label>Gambar</label>
                    <input type="file" id="editGambar" name="gambar">
                </div>
            </div>

            <h3 class="desc-title">Deskripsi Paket Wisata</h3>

            <div class="tambah-grid">
                <div class="tambah-group">
                    <label>Fasilitas yang didapatkan</label>
                    <textarea class="auto-textarea" id="editFasilitas" name="fasilitas" oninput="autoResize(this)"></textarea>
                </div>

                <div class="tambah-group">
                    <label>Deskripsi Paket Wisata</label>
                    <textarea class="auto-textarea" id="editDeskripsi" name="deskripsi" oninput="autoResize(this)"></textarea>
                </div>
            </div>

            <div class="tambah-actions">
                <button type="button" class="btn-modal-secondary" onclick="closeEdit()">Kembali</button>
                <button type="submit" class="btn-modal-primary">Ubah</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div class="delete-modal-overlay" id="modalHapus">
    <div class="delete-modal-box">
        <div class="delete-header">
            <div class="warning-icon">!</div>
            <h2>Hapus Paket Wisata</h2>
        </div>

        <div class="delete-body">
            <h3>Apakah Anda yakin ingin menghapus paket wisata ini?</h3>
            <p>Paket wisata yang dihapus tidak dapat dikembalikan.</p>
        </div>

        <div class="delete-actions">
            <button type="button" class="btn-cancel-delete" onclick="closeHapus()">Batal</button>

            <form id="formHapus" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Hapus Paket Wisata</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function openTambah() {
    document.getElementById('modalTambah').classList.add('show');
}

function closeTambah() {
    document.getElementById('modalTambah').classList.remove('show');
}

function openEdit(
    id,
    namaPaket,
    tipe,
    idKota,
    kapasitas,
    minPeserta,
    durasi,
    idKendaraan,
    harga,
    fasilitas,
    deskripsi,
    status,
    tanggalBerangkat,
    tanggalKembali,
    titikJemput,
    kotaLayanan
) {
    document.getElementById('editNamaPaket').value    = namaPaket;
    document.getElementById('editTipe').value         = tipe;
    document.getElementById('editKapasitas').value    = kapasitas ?? '';
    document.getElementById('editMinPeserta').value   = minPeserta ?? '';
    document.getElementById('editDurasi').value       = durasi;
    document.getElementById('editKendaraan').value    = idKendaraan ?? '';
    document.getElementById('editHarga').value        = harga;
    document.getElementById('editFasilitas').value    = fasilitas ?? '';
    document.getElementById('editDeskripsi').value    = deskripsi ?? '';
    document.getElementById('editStatus').value       = status ?? 'aktif';
    document.getElementById('editTanggalBerangkat').value = tanggalBerangkat ?? '';
    document.getElementById('editTanggalKembali').value   = tanggalKembali ?? '';

    $('#editKota').val(idKota).trigger('change');
    $('#editTitikJemput').val(titikJemput).trigger('change');
    $('#editKotaLayanan').val(kotaLayanan).trigger('change');

    document.getElementById('editForm').action =
        '/dashboard/superadmin/kelola-paket-wisata/update/' + id;

    toggleEditFields();
    document.getElementById('modalEdit').classList.add('show');
}

function closeEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}

function openHapus(id) {
    document.getElementById('formHapus').action =
        '/dashboard/superadmin/kelola-paket-wisata/delete/' + id;

    document.getElementById('modalHapus').classList.add('show');
}

function closeHapus() {
    document.getElementById('modalHapus').classList.remove('show');
}

function toggleTambahFields() {
    const tipe = document.getElementById('tambahTipe').value;
    const openFields = document.querySelectorAll('.open-trip-field');
    const paketFields = document.querySelectorAll('.paket-field');

    openFields.forEach(field => {
        field.style.display = tipe === 'open_trip' ? '' : 'none';
    });

    paketFields.forEach(field => {
        field.style.display = tipe === 'paket' ? '' : 'none';
    });

    document.getElementById('tambahTanggalBerangkat').required = tipe === 'open_trip';
    document.getElementById('tambahTanggalKembali').required   = tipe === 'open_trip';
    document.getElementById('tambahKapasitas').required        = tipe === 'open_trip';
    document.getElementById('tambahKendaraan').required        = tipe === 'open_trip';
}

function toggleEditFields() {
    const tipe = document.getElementById('editTipe').value;
    const openFields = document.querySelectorAll('.edit-open-trip-field');
    const paketFields = document.querySelectorAll('.edit-paket-field');

    openFields.forEach(field => {
        field.style.display = tipe === 'open_trip' ? '' : 'none';
    });

    paketFields.forEach(field => {
        field.style.display = tipe === 'paket' ? '' : 'none';
    });

    document.getElementById('editTanggalBerangkat').required = tipe === 'open_trip';
    document.getElementById('editTanggalKembali').required   = tipe === 'open_trip';
    document.getElementById('editKapasitas').required        = tipe === 'open_trip';
    document.getElementById('editMinPeserta').required       = tipe === 'paket';
    document.getElementById('editKendaraan').required        = tipe === 'open_trip';
}

function sinkronKendaraanDenganKapasitas(kapasitasInput, kendaraanSelect) {
    if (!kapasitasInput || !kendaraanSelect) return;

    function updatePilihanKendaraan() {
        const kapasitas = parseInt(kapasitasInput.value) || 0;
        let kandidat = null;
        let kapasitasTerkecil = Infinity;

        Array.from(kendaraanSelect.options).forEach(option => {
            if (option.value === "") return;

            const kapasitasKendaraan = parseInt(option.getAttribute('data-kapasitas')) || 0;

            if (kapasitas === 0 || kapasitasKendaraan >= kapasitas) {
                option.hidden = false;

                if (kapasitasKendaraan < kapasitasTerkecil) {
                    kapasitasTerkecil = kapasitasKendaraan;
                    kandidat = option;
                }
            } else {
                option.hidden = true;
            }
        });

        if (kandidat) {
            kendaraanSelect.value = kandidat.value;
        } else {
            kendaraanSelect.value = "";
        }
    }

    kapasitasInput.addEventListener('input', updatePilihanKendaraan);
    kapasitasInput.addEventListener('change', updatePilihanKendaraan);

    updatePilihanKendaraan();
}

document.addEventListener("DOMContentLoaded", function () {
    $('#tambahKota').select2({
        placeholder: '-- Pilih Kota --',
        width: '100%',
        dropdownParent: $('#modalTambah')
    });

    $('#editKota').select2({
        placeholder: '-- Pilih Kota --',
        width: '100%',
        dropdownParent: $('#modalEdit')
    });

    $('#tambahTitikJemput').select2({
        placeholder: '-- Pilih atau ketik titik jemput --',
        width: '100%',
        dropdownParent: $('#modalTambah'),
        tags: true, // 🔥 WAJIB
        tokenSeparators: [',']
    });

    $('#tambahKotaLayanan').select2({
        placeholder: '-- Pilih Kota Layanan --',
        width: '100%',
        dropdownParent: $('#modalTambah')
    });

    $('#editTitikJemput').select2({
        placeholder: '-- Pilih atau ketik titik jemput --',
        width: '100%',
        dropdownParent: $('#modalEdit'),
        tags: true,
        tokenSeparators: [',']
    });

    $('#editKotaLayanan').select2({
        placeholder: '-- Pilih Kota Layanan --',
        width: '100%',
        dropdownParent: $('#modalEdit')
    });

    const tambahTipe = document.getElementById('tambahTipe');
    const editTipe   = document.getElementById('editTipe');

    tambahTipe.addEventListener('change', toggleTambahFields);
    editTipe.addEventListener('change', toggleEditFields);

    toggleTambahFields();

    sinkronKendaraanDenganKapasitas(
        document.getElementById('tambahKapasitas'),
        document.getElementById('tambahKendaraan')
    );

    sinkronKendaraanDenganKapasitas(
        document.getElementById('editKapasitas'),
        document.getElementById('editKendaraan')
    );
});
</script>
@endpush