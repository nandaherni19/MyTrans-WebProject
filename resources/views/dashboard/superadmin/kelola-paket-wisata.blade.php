@extends('layouts.admin')
@section('title', 'Kelola Paket Wisata dan Destinasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-paket-wisata.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div>
            <h1>Kelola Paket Wisata dan Destinasi</h1>
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
                                    ❌ Lewat
                                @endif
                            </p>
                            <p class="paket-location">
                                📍 {{ $paket->trayek->kotaAsal->nama_kota ?? '-' }} → {{ $paket->trayek->kotaTujuan->nama_kota ?? '-' }}
                            </p>

                            <p class="paket-destination">
                                Tujuan: {{ $paket->trayek->kotaTujuan->nama_kota ?? '-' }}
                            </p>

                            <p class="paket-date">
                                📅 {{ \Carbon\Carbon::parse($paket->tanggal_keberangkatan)->format('d M Y') }}
                            </p>

                            <p class="paket-capacity">
                                {{ $paket->durasi }} • Kapasitas {{ $paket->kapasitas }} orang
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
                                "{{ $paket->kapasitas }}",
                                @json($paket->durasi),
                                "{{ $paket->id_trayek }}",
                                "{{ $paket->id_kendaraan }}",
                                "{{ $paket->harga }}",
                                @json($paket->fasilitas_didapat),
                                @json($paket->deskripsi),
                                @json($paket->status),
                                "{{ $paket->tanggal_keberangkatan }}"
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
                        <label>Trayek <span>*</span></label>
                        <select name="id_trayek" required>
                            <option value="">-- Pilih Trayek --</option>
                            @foreach($trayeks as $trayek)
                                <option value="{{ $trayek->id_trayek }}">
                                    {{ $trayek->kode_trayek }} - {{ $trayek->kotaAsal->nama_kota ?? '-' }} → {{ $trayek->kotaTujuan->nama_kota ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Tanggal Keberangkatan <span>*</span></label>
                        <input type="date" name="tanggal_keberangkatan" required>
                    </div>
                    
                    <div class="tambah-group">
                        <label>Nama paket wisata <span>*</span></label>
                        <input type="text" name="nama_paket" placeholder="Pantai Watu Karung" required>
                    </div>

                    <div class="tambah-group">
                        <label>Kapasitas <span>*</span></label>
                        <input type="number" name="kapasitas" placeholder="50" min="1" required>
                    </div>

                    <div class="tambah-group">
                        <label>Kendaraan <span>*</span></label>
                        <select name="id_kendaraan" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($kendaraans as $kendaraan)
                                <option value="{{ $kendaraan->id_kendaraan }}" data-kapasitas="{{ $kendaraan->kapasitas }}">
                                    {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Durasi <span>*</span></label>
                        <input type="text" name="durasi" placeholder="2 Hari 1 Malam" required>
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
                        <textarea name="fasilitas_didapat" class="auto-textarea"></textarea>
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
                        <label>Trayek <span>*</span></label>
                        <select id="editTrayek" name="id_trayek" required>
                            <option value="">-- Pilih Trayek --</option>
                            @foreach($trayeks as $trayek)
                                <option value="{{ $trayek->id_trayek }}">
                                    {{ $trayek->kode_trayek }} - {{ $trayek->kotaAsal->nama_kota ?? '-' }} → {{ $trayek->kotaTujuan->nama_kota ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Tanggal Keberangkatan <span>*</span></label>
                        <input type="date" id="editTanggal" name="tanggal_keberangkatan" required>
                    </div>

                    <div class="tambah-group">
                        <label>Nama paket wisata <span>*</span></label>
                        <input type="text" id="editNamaPaket" name="nama_paket" required>
                    </div>

                    <div class="tambah-group">
                        <label>Kapasitas <span>*</span></label>
                        <input type="number" id="editKapasitas" name="kapasitas" min="1" required>
                    </div>

                    <div class="tambah-group">
                        <label>Kendaraan <span>*</span></label>
                        <select name="id_kendaraan" id="editkendaraan" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($kendaraans as $kendaraan)
                                <option value="{{ $kendaraan->id_kendaraan }}" data-kapasitas="{{ $kendaraan->kapasitas }}">
                                    {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Durasi <span>*</span></label>
                        <input type="text" id="editDurasi" name="durasi" required>
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
                        <label>Fasilitas yang didapatkan <span>*</span></label>
                        <textarea class="auto-textarea" id="editFasilitasDidapat" name="fasilitas_didapat" oninput="autoResize(this)" required></textarea>
                    </div>

                    <div class="tambah-group">
                        <label>Deskripsi Paket Wisata <span>*</span></label>
                        <textarea class="auto-textarea" id="editDeskripsi" name="deskripsi" oninput="autoResize(this)" required></textarea>
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

    function openEdit(id, nama_paket, kapasitas, durasi, trayek, kendaraan, harga, fasilitas, deskripsi, status, tanggal) {
        document.getElementById('editNamaPaket').value = nama_paket;
        document.getElementById('editKapasitas').value = kapasitas;
        document.getElementById('editDurasi').value = durasi;
        document.getElementById('editTrayek').value = trayek;
        document.getElementById('editkendaraan').value = kendaraan;
        document.getElementById('editHarga').value = harga;
        document.getElementById('editFasilitasDidapat').value = fasilitas ?? '';
        document.getElementById('editDeskripsi').value = deskripsi ?? '';
        document.getElementById('editStatus').value = status ?? 'aktif';
        document.getElementById('editTanggal').value = tanggal;

        document.getElementById('editForm').action =
            '/dashboard/superadmin/kelola-paket-wisata/update/' + id;

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

document.addEventListener("DOMContentLoaded", function () {

    // TAMBAH
    const kendaraanTambah = document.querySelector('#modalTambah select[name="id_kendaraan"]');
    const kapasitasTambah = document.querySelector('#modalTambah input[name="kapasitas"]');

    if (kendaraanTambah) {
        kendaraanTambah.addEventListener('change', function () {
            let selected = this.options[this.selectedIndex];
            let kapasitas = selected.getAttribute('data-kapasitas');

            if (kapasitas) {
                kapasitasTambah.value = kapasitas;
                kapasitasTambah.max = kapasitas;
            }
        });
    }

    // EDIT
    const kendaraanEdit = document.getElementById('editkendaraan');
    const kapasitasEdit = document.getElementById('editKapasitas');

    if (kendaraanEdit) {
        kendaraanEdit.addEventListener('change', function () {
            let selected = this.options[this.selectedIndex];
            let kapasitas = selected.getAttribute('data-kapasitas');

            if (kapasitas) {
                kapasitasEdit.value = kapasitas;
                kapasitasEdit.max = kapasitas;
            }
        });
    }

});
</script>
@endpush