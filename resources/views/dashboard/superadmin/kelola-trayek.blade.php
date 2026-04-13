@extends('layouts.admin')
@section('title', 'Kelola Trayek')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-pengguna.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="content-header">
    <div>
        <h1>Kelola Trayek</h1>
    </div>

    <div class="header-actions">
        <form method="GET" action="{{ route('dashboard.superadmin.kelola-trayek') }}" class="filter-form">
            <input
                type="text"
                name="search"
                placeholder="Cari kode / kota asal / kota tujuan..."
                value="{{ request('search') }}"
            >
            <button type="submit">Filter</button>
        </form>

        <button class="btn-add" onclick="openTambah()">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Trayek</span>
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
    <section class="user-card">
        <h3>Daftar Trayek</h3>

        <table class="user-table">
            <thead>
                <tr>
                    <th>Kode Trayek</th>
                    <th>Kota Asal</th>
                    <th>Kota Tujuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($trayeks as $trayek)
                    <tr>
                        <td data-label="Kode Trayek">{{ $trayek->kode_trayek }}</td>
                        <td data-label="Kota Asal">{{ $trayek->kota_asal ?? '-' }}</td>
                        <td data-label="Kota Tujuan">{{ $trayek->kota_tujuan ?? '-' }}</td>
                        <td data-label="Aksi" class="action-cell">
                            <button
                                type="button"
                                class="btn-edit"
                                onclick="openEdit(
                                    '{{ $trayek->id_trayek }}',
                                    '{{ $trayek->id_kota_asal }}',
                                    '{{ $trayek->id_kota_tujuan }}'
                                )"
                            >
                                <i class="fa-solid fa-pen"></i>
                                <span>Edit</span>
                            </button>

                            <button
                                type="button"
                                class="btn-delete"
                                onclick="openHapus('{{ $trayek->id_trayek }}')"
                            >
                                <i class="fa-solid fa-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;">Belum ada data trayek</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

{{-- tambah --}}
<div id="modalTambah" class="overlay">
    <div class="modal-box">
        <h2>Tambah Trayek</h2>

        <form method="POST" action="{{ route('dashboard.superadmin.kelola-trayek.store') }}">
            @csrf

            <div class="form-group">
                <label>Kota Asal</label>
                <select name="id_kota_asal" required>
                    <option value="">Pilih Kota Asal</option>
                    @foreach($kotas as $kota)
                        <option value="{{ $kota->id_kota }}">{{ $kota->nama_kota }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kota Tujuan</label>
                <select name="id_kota_tujuan" required>
                    <option value="">Pilih Kota Tujuan</option>
                    @foreach($kotas as $kota)
                        <option value="{{ $kota->id_kota }}">{{ $kota->nama_kota }}</option>
                    @endforeach
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn-kembali" onclick="closeTambah()">Kembali</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- edit --}}
<div id="modalEdit" class="overlay">
    <div class="modal-box">
        <h2>Edit Trayek</h2>

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Kota Asal</label>
                <select id="editKotaAsal" name="id_kota_asal" required>
                    <option value="">Pilih Kota Asal</option>
                    @foreach($kotas as $kota)
                        <option value="{{ $kota->id_kota }}">{{ $kota->nama_kota }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Kota Tujuan</label>
                <select id="editKotaTujuan" name="id_kota_tujuan" required>
                    <option value="">Pilih Kota Tujuan</option>
                    @foreach($kotas as $kota)
                        <option value="{{ $kota->id_kota }}">{{ $kota->nama_kota }}</option>
                    @endforeach
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn-kembali" onclick="closeEdit()">Kembali</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- hapus --}}
<div id="modalHapus" class="overlay">
    <div class="delete-box">
        <div class="delete-header">
            <div class="warning-icon">!</div>
            <h2>Hapus Trayek</h2>
        </div>

        <div class="delete-body">
            <h3>Apakah Anda yakin ingin menghapus trayek ini?</h3>
            <p>Data trayek yang dihapus tidak dapat dikembalikan.</p>
        </div>

        <div class="button-group" style="padding: 20px 32px 28px;">
            <button class="btn-batal" type="button" onclick="closeHapus()">Batal</button>
            <form id="formHapus" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-hapus">Hapus Trayek</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openTambah() {
    document.getElementById('modalTambah').classList.add('show');
}

function closeTambah() {
    document.getElementById('modalTambah').classList.remove('show');
}

function openEdit(id, idKotaAsal, idKotaTujuan) {
    document.getElementById('editKotaAsal').value = idKotaAsal;
    document.getElementById('editKotaTujuan').value = idKotaTujuan;
    document.getElementById('formEdit').action =
        '/dashboard/superadmin/kelola-trayek/update/' + id;

    document.getElementById('modalEdit').classList.add('show');
}

function closeEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}

function openHapus(id) {
    document.getElementById('formHapus').action =
        '/dashboard/superadmin/kelola-trayek/delete/' + id;

    document.getElementById('modalHapus').classList.add('show');
}

function closeHapus() {
    document.getElementById('modalHapus').classList.remove('show');
}
</script>
@endpush