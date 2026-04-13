@extends('layouts.admin')
@section('title', 'Kelola Request Wisata')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola-request-wisata.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
<div class="content-header">
    <div>
        <h1>Kelola Request Wisata</h1>
        <p class="subtitle">Kelola permintaan wisata dari user</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="request-card">
    <div class="table-wrapper">
        <table class="request-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Rute</th>
                    <th>Tanggal</th>
                    <th>Peserta</th>
                    <th>Durasi</th>
                    <th>Estimasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $item)

                <tr>
                    <td data-label="User">{{ optional($item->user)->nama ?? '-' }}</td>
                    <td data-label="Rute">
                        {{ optional($item->kotaAsal)->nama_kota ?? '-' }} →
                        {{ optional($item->kotaTujuan)->nama_kota ?? '-' }}
                    </td>
                    <td data-label="Tanggal">{{ $item->tanggal_keberangkatan ?? '-' }}</td>
                    <td data-label="Peserta">{{ $item->jumlah_peserta ?? '-' }} orang</td>
                    <td data-label="Durasi">{{ $item->durasi ?? '-' }}</td>

                    {{-- Estimasi: diisi admin lewat tombol Edit --}}
                    <td data-label="Estimasi">
                        {{ $item->estimasi_harga
                            ? 'Rp ' . number_format($item->estimasi_harga, 0, ',', '.')
                            : 'Belum diisi' }}
                    </td>

                    {{-- Badge status --}}
                    <td data-label="Status">
                        @php
                            $badgeClass = match($item->status_request) {
                                'pending'   => 'badge-pending',
                                'diproses'  => 'badge-diproses',
                                'disetujui' => 'badge-disetujui',
                                'ditolak'   => 'badge-ditolak',
                                'selesai'   => 'badge-selesai',
                                default     => ''
                            };
                        @endphp
                        <span class="{{ $badgeClass }}">{{ ucfirst($item->status_request) }}</span>
                    </td>

                    <td data-label="Aksi" class="action-cell">
                        <div class="aksi-wrapper">
                            @php
                                $estimasiFormatted = $item->estimasi_harga
                                    ? 'Rp ' . number_format($item->estimasi_harga, 0, ',', '.')
                                    : 'Belum diisi';
                            @endphp

                            {{-- Detail selalu bisa dilihat --}}
                            <button type="button" class="btn-detail"
                                onclick="openDetail(
                                    {{ Js::from(optional($item->user)->nama ?? '-') }},
                                    {{ Js::from(optional($item->kotaAsal)->nama_kota ?? '-') }},
                                    {{ Js::from(optional($item->kotaTujuan)->nama_kota ?? '-') }},
                                    {{ Js::from($item->tanggal_keberangkatan ?? '-') }},
                                    {{ Js::from($item->jumlah_peserta ?? '-') }},
                                    {{ Js::from($item->durasi ?? '-') }},
                                    {{ Js::from($item->catatan ?? '-') }},
                                    {{ Js::from($estimasiFormatted) }},
                                    {{ Js::from($item->status_request ?? '-') }}
                                )">
                                Detail
                            </button>

                            {{-- Edit estimasi hanya saat pending atau diproses --}}
                            @if(in_array($item->status_request, ['pending', 'diproses']))
                            <button type="button" class="btn-edit"
                                onclick="openEdit(
                                    {{ Js::from($item->id_request) }},
                                    {{ Js::from($item->estimasi_harga) }},
                                    {{ Js::from($item->status_request) }}
                                )">
                                Edit
                            </button>
                            @endif

                            {{-- ACC dan Tolak hanya tampil jika estimasi sudah diisi --}}
                            @if($item->estimasi_harga && $item->status_request === 'diproses')
                            <form action="{{ route('dashboard.superadmin.kelola-request.acc', $item->id_request) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-acc">ACC</button>
                            </form>

                            <form action="{{ route('dashboard.superadmin.kelola-request.reject', $item->id_request) }}" method="POST" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-reject">Tolak</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Belum ada request wisata</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modalDetail" class="overlay">
    <div class="modal-box">
        <h2>Detail Request Wisata</h2>

        <div class="form-group">
            <label>Nama User</label>
            <input type="text" id="detailUser" readonly>
        </div>

        <div class="form-group">
            <label>Kota Asal</label>
            <input type="text" id="detailAsal" readonly>
        </div>

        <div class="form-group">
            <label>Kota Tujuan</label>
            <input type="text" id="detailTujuan" readonly>
        </div>

        <div class="form-group">
            <label>Tanggal Keberangkatan</label>
            <input type="text" id="detailTanggal" readonly>
        </div>

        <div class="form-group">
            <label>Jumlah Peserta</label>
            <input type="text" id="detailPeserta" readonly>
        </div>

        <div class="form-group">
            <label>Durasi</label>
            <input type="text" id="detailDurasi" readonly>
        </div>

        <div class="form-group">
            <label>Catatan</label>
            <textarea id="detailCatatan" readonly></textarea>
        </div>

        <div class="form-group">
            <label>Estimasi Harga</label>
            <input type="text" id="detailEstimasi" readonly>
        </div>

        <div class="form-group">
            <label>Status</label>
            <input type="text" id="detailStatus" readonly>
        </div>

        <div class="button-group">
            <button type="button" class="btn-kembali" onclick="closeDetail()">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="overlay">
    <div class="modal-box">
        <h2>Edit Request Wisata</h2>

        <form id="formEditRequest" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Estimasi Harga</label>
                <input type="number" name="estimasi_harga" id="editEstimasiHarga" min="0" required>
            </div>

            <div class="form-group">
                <label>Status Request</label>
                <select name="status_request" id="editStatusRequest" required>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn-kembali" onclick="closeEdit()">Batal</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDetail(user, asal, tujuan, tanggal, peserta, durasi, catatan, estimasi, status) {
    document.getElementById('detailUser').value = user;
    document.getElementById('detailAsal').value = asal;
    document.getElementById('detailTujuan').value = tujuan;
    document.getElementById('detailTanggal').value = tanggal;
    document.getElementById('detailPeserta').value = peserta + ' orang';
    document.getElementById('detailDurasi').value = durasi;
    document.getElementById('detailCatatan').value = catatan;
    document.getElementById('detailEstimasi').value = estimasi;
    document.getElementById('detailStatus').value = status;
    document.getElementById('modalDetail').classList.add('show');
}

function closeDetail() {
    document.getElementById('modalDetail').classList.remove('show');
}

function openEdit(id, estimasi, status) {
    document.getElementById('editEstimasiHarga').value = estimasi ?? '';
    document.getElementById('editStatusRequest').value = status ?? 'pending';
    document.getElementById('formEditRequest').action = '/dashboard/superadmin/kelola-request/update/' + id;
    document.getElementById('modalEdit').classList.add('show');
}

function closeEdit() {
    document.getElementById('modalEdit').classList.remove('show');
}
</script>
@endpush