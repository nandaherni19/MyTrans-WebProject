@extends('layouts.admin')
@section('title', 'Kelola Destinasi Wisata')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-lokasi.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@php
    $section = $section ?? 'provinsi';
    $mode = $mode ?? 'list';

    $isProvinsi = $section === 'provinsi';
    $isKota = $section === 'kota';
@endphp

@section('content')
<div class="page-top">
    <h1>Kelola Lokasi Wisata</h1>
</div>

<div class="tabs">
    <a href="{{ url('/dashboard/superadmin/kelola-destinasi/provinsi') }}" class="{{ $isProvinsi ? 'active' : '' }}">Provinsi</a>
    <span>|</span>
    <a href="{{ url('/dashboard/superadmin/kelola-destinasi/kota') }}" class="{{ $isKota ? 'active' : '' }}">Kota</a>
</div>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div style="color:red; margin-bottom:16px;">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<!-- provinsi -->
<section class="card-box">
    @if($isProvinsi)
        <div class="card-header">
            <div>
                <h2>Manajemen Provinsi</h2>
                <p>Kontrol daftar provinsi yang muncul di dropdown filter</p>
            </div>
            <button type="button" class="btn-tambah" onclick="showTambahProvinsi()">+ Tambah Provinsi</button>
        </div>

        {{-- FORM TAMBAH --}}
        <form id="formTambahProvinsi"
            action="{{ route('dashboard.superadmin.kelola-destinasi.provinsi.store') }}"
            method="POST"
            class="provinsi-add-wrap"
            style="display:none;">
            @csrf
            <div class="provinsi-add-row">
                <input type="text" name="nama_provinsi" placeholder="Nama Provinsi" required>
                <button type="submit" class="btn-simpan">Simpan</button>
                <button type="button" class="btn-batal" onclick="hideTambahProvinsi()">Keluar</button>
            </div>
        </form>

        {{-- FORM EDIT --}}
        <form id="formEditProvinsiInline"
            method="POST"
            class="provinsi-add-wrap"
            style="display:none;">
            @csrf
            @method('PUT')
            <div class="provinsi-add-row">
                <input type="text" id="editInlineNamaProvinsi" name="nama_provinsi" placeholder="Nama Provinsi" required>
                <button type="submit" class="btn-simpan">Update</button>
                <button type="button" class="btn-batal" onclick="hideEditProvinsi()">X</button>
            </div>
        </form>

        {{-- LIST PROVINSI --}}
        <div class="item-list">
            @forelse($provinsis as $provinsi)
                <div class="item-card">
                    <div class="item-info">
                        <h3>{{ $provinsi->nama_provinsi }}</h3>
                        <p>{{ $provinsi->kota_count ?? 0 }} kota terdaftar</p>
                    </div>

                    <div class="item-actions">
                        <button type="button"
                            class="icon-edit btn-edit-provinsi"
                            data-id="{{ $provinsi->id_provinsi }}"
                            data-nama="{{ $provinsi->nama_provinsi }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button type="button"
                            class="icon-delete btn-delete-provinsi"
                            data-id="{{ $provinsi->id_provinsi }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="item-card">
                    <div class="item-info">
                        <h3>Belum ada data provinsi</h3>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

<!-- ===================== KOTA =====================  -->
@if($isKota)
    <div class="card-header">
        <div>
            <h2>Manajemen Kota</h2>
            <p>Kontrol daftar Kota yang muncul di dropdown filter</p>
        </div>
        <button type="button" class="btn-tambah" onclick="showTambahKota()">+ Tambah Kota</button>
    </div>

    {{-- FORM TAMBAH KOTA --}}
    <form id="formTambahKota"
        action="{{ route('dashboard.superadmin.kelola-destinasi.kota.store') }}"
        method="POST"
        class="form-box"
        style="display:none;">
        @csrf
        <div class="form-grid two-col">
            <input type="text" name="nama_kota" placeholder="Nama Kota" required>

            <div class="select-wrap">
                <select name="id_provinsi" required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsis as $provinsi)
                        <option value="{{ $provinsi->id_provinsi }}">{{ $provinsi->nama_provinsi }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>

        <div class="form-actions-inline">
            <button type="submit" class="btn-simpan">Simpan</button>
            <button type="button" class="btn-batal" onclick="hideTambahKota()">X</button>
        </div>
    </form>

    {{-- FORM EDIT KOTA --}}
    <form id="formEditKotaInline"
        method="POST"
        class="form-box"
        style="display:none;">
        @csrf
        @method('PUT')
        <div class="form-grid two-col">
            <input type="text" id="editNamaKotaInline" name="nama_kota" placeholder="Nama Kota" required>

            <div class="select-wrap">
                <select id="editProvinsiKotaInline" name="id_provinsi" required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsis as $provinsi)
                        <option value="{{ $provinsi->id_provinsi }}">{{ $provinsi->nama_provinsi }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>

        <div class="form-actions-inline">
            <button type="submit" class="btn-simpan">Update</button>
            <button type="button" class="btn-batal" onclick="hideEditKota()">X</button>
        </div>
    </form>

    {{-- LIST KOTA --}}
    <div class="item-list">
        @forelse($kotas as $kota)
            <div class="item-card">
                <div class="item-info">
                    <h3>{{ $kota->nama_kota }}</h3>
                    <p>{{ $kota->provinsi->nama_provinsi ?? '-' }}</p>
                </div>

                <div class="item-actions">
                    <button type="button"
                        class="icon-edit btn-edit-kota"
                        data-id="{{ $kota->id_kota }}"
                        data-nama="{{ $kota->nama_kota }}"
                        data-provinsi="{{ $kota->id_provinsi }}">
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    <button type="button"
                        class="icon-delete btn-delete-kota"
                        data-id="{{ $kota->id_kota }}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="item-card">
                <div class="item-info">
                    <h3>Belum ada data kota</h3>
                </div>
            </div>
        @endforelse
    </div>
    @endif
    </section>
    {{-- MODAL HAPUS --}}
<div id="modalDeleteGeneric" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:2rem 1.75rem; max-width:380px; width:90%; text-align:center; font-family:'Poppins',sans-serif;">
        <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
            <i class="fa-solid fa-trash" style="color:#e24b4a; font-size:20px;"></i>
        </div>
        <h2 id="deleteTitle" style="font-size:16px; font-weight:600; margin:0 0 0.4rem; color:#1a1a1a;"></h2>
        <p id="deleteQuestion" style="font-size:13px; color:#555; margin:0 0 0.25rem;"></p>
        <p id="deleteDesc" style="font-size:12px; color:#aaa; margin:0 0 1.5rem;"></p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="closeModal('modalDeleteGeneric')"
                style="padding:8px 24px; border-radius:8px; border:1px solid #ddd; background:transparent; cursor:pointer; font-size:13px; font-family:'Poppins',sans-serif; color:#555;">
                Batal
            </button>
            <form id="formDeleteGeneric" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="padding:8px 24px; border-radius:8px; border:none; background:#e24b4a; color:#fff; cursor:pointer; font-size:13px; font-weight:600; font-family:'Poppins',sans-serif;">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Provinsi
document.addEventListener('DOMContentLoaded', function () {
    const formTambah = document.getElementById('formTambahProvinsi');
    const formEdit = document.getElementById('formEditProvinsiInline');
    const inputEdit = document.getElementById('editInlineNamaProvinsi');
    const btnTambah = document.querySelector('.btn-tambah');

    if (btnTambah) {
        btnTambah.addEventListener('click', function () {
            if (formTambah) formTambah.style.display = 'block';
            if (formEdit) formEdit.style.display = 'none';
        });
    }

    window.hideTambahProvinsi = function () {
        if (formTambah) formTambah.style.display = 'none';
    };

    window.hideEditProvinsi = function () {
        if (formEdit) formEdit.style.display = 'none';
    };

    document.querySelectorAll('.btn-edit-provinsi').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            if (formTambah) formTambah.style.display = 'none';
            if (formEdit) formEdit.style.display = 'block';
            if (inputEdit) inputEdit.value = nama;

            if (formEdit) {
                formEdit.action = '/dashboard/superadmin/kelola-destinasi/provinsi/update/' + id;
            }
        });
    });

    document.querySelectorAll('.btn-delete-provinsi').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            const title = document.getElementById('deleteTitle');
            const question = document.getElementById('deleteQuestion');
            const desc = document.getElementById('deleteDesc');
            const formDelete = document.getElementById('formDeleteGeneric');
            const modal = document.getElementById('modalDeleteGeneric');

            if (title) title.textContent = 'Hapus Provinsi';
            if (question) question.textContent = 'Apakah Anda yakin ingin menghapus provinsi ini?';
            if (desc) desc.textContent = 'Data provinsi yang dihapus tidak dapat dikembalikan.';
            if (formDelete) formDelete.action = '/dashboard/superadmin/kelola-destinasi/provinsi/delete/' + id;
            if (modal) modal.style.display = 'flex';
        });
    });

    window.closeModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    };
});

// kota
const formTambahKota = document.getElementById('formTambahKota');
const formEditKota = document.getElementById('formEditKotaInline');

window.showTambahKota = function () {
    if (formTambahKota) formTambahKota.style.display = 'block';
    if (formEditKota) formEditKota.style.display = 'none';
};

window.hideTambahKota = function () {
    if (formTambahKota) formTambahKota.style.display = 'none';
};

window.hideEditKota = function () {
    if (formEditKota) formEditKota.style.display = 'none';
};

document.querySelectorAll('.btn-edit-kota').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const nama = this.dataset.nama;
        const provinsi = this.dataset.provinsi;

        if (formTambahKota) formTambahKota.style.display = 'none';
        if (formEditKota) formEditKota.style.display = 'block';

        document.getElementById('editNamaKotaInline').value = nama;
        document.getElementById('editProvinsiKotaInline').value = provinsi;

        formEditKota.action = '/dashboard/superadmin/kelola-destinasi/kota/update/' + id;
    });
});

document.querySelectorAll('.btn-delete-kota').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;

        document.getElementById('deleteTitle').textContent = 'Hapus Kota';
        document.getElementById('deleteQuestion').textContent = 'Apakah Anda yakin ingin menghapus kota ini?';
        document.getElementById('deleteDesc').textContent = 'Data kota yang dihapus tidak dapat dikembalikan.';
        document.getElementById('formDeleteGeneric').action =
            '/dashboard/superadmin/kelola-destinasi/kota/delete/' + id;
        document.getElementById('modalDeleteGeneric').style.display = 'flex';
    });
});
</script>
@endpush