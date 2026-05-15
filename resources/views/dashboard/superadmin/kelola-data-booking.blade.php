@extends('layouts.admin')
@section('title', 'Kelola Data Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/kelola-data-booking.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@section('content')
    @php
        $bookingData = $bookingData ?? collect();
        $currentId = $id ?? ($bookingData->keys()->first() ?? null);
        $current = $current ?? ($currentId ? ($bookingData[$currentId] ?? null) : null);
    @endphp

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color:red">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div style="color:red">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- ================= HALAMAN LIST ================= --}}
    @if(($page ?? 'index') === 'index')
        <div class="booking-topbar">
            <div class="booking-title">
                <h1>Data Booking</h1>
                <p>Kelola dan Konfirmasi Data Booking</p>
            </div>
            <button type="button" class="btn-save" onclick="openTambahBooking()">
                + Tambah Booking
            </button>
        </div>

        <div class="booking-filter-wrap">
            <div class="booking-filter">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="searchBooking" placeholder="Cari ID booking, nama, paket, kendaraan..."
                        style="width:100%; border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit;">
                </div>

                <div class="filter-dropdown">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="date" id="filterTanggal" value="{{ date('Y-m-d') }}"
                        style="border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit; cursor:pointer;">
                </div>
                {{-- FILTER TIPE BOOKING --}}
                <div class="filter-dropdown">
                    <i class="fa-solid fa-layer-group"></i>

                    <select id="filterTipe"
                        style="border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit; cursor:pointer;">
                        <option value="">Semua Tipe</option>
                        <option value="open_trip">Open Trip</option>
                        <option value="paket">Paket</option>
                    </select>
                </div>

                {{-- FILTER JENIS TANGGAL --}}
                <div class="filter-dropdown">
                    <i class="fa-regular fa-calendar-days"></i>

                    <select id="filterJenisTanggal"
                        style="border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit; cursor:pointer;">

                        <option value="transaksi">Tanggal Booking</option>
                        <option value="berangkat">Tanggal Berangkat</option>

                    </select>
                </div>

                <!-- {{-- FILTER PERIODE --}}
                        <div class="filter-dropdown">
                            <i class="fa-regular fa-clock"></i>

                            <select id="filterPeriode"
                                style="border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit; cursor:pointer;">
                                <option value="today"      {{ request('periode', 'today') === 'today'      ? 'selected' : '' }}>Hari Ini</option>
                                <option value="week"       {{ request('periode') === 'week'                ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="month"      {{ request('periode') === 'month'               ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="last_month" {{ request('periode') === 'last_month'          ? 'selected' : '' }}>Bulan Lalu</option>
                                <option value="all"        {{ request('periode') === 'all'                 ? 'selected' : '' }}>Semua</option>
                            </select>
                        </div> -->
            </div>
        </div>

        <div class="booking-table-card scrollable-card">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>ID Booking</th>
                        <th>Pelanggan</th>
                        <th>Paket</th>
                        <th>Tanggal</th>
                        <th>Peserta</th>
                        <th>Kendaraan</th>
                        <th>Status Booking</th>
                        <th>Pembayaran</th>
                        <th class="aksi-header">Aksi</th>
                    </tr>
                </thead>

                <tbody id="bookingTableBody">
                    @php $order = 1; @endphp

                    @forelse($bookingData as $item)
                        <tr class="booking-row"
                            data-search="{{ strtolower($item['id_booking'] . ' ' . $item['nama'] . ' ' . $item['wisata'] . ' ' . $item['kendaraan']) }}"
                            data-date="{{ $item['tanggal_sort'] ? \Carbon\Carbon::parse($item['tanggal_sort'])->format('Y-m-d') : '' }}"
                            data-transaksi="{{ $item['tanggal_transaksi'] ?? '' }}" data-tipe="{{ $item['tipe_booking'] ?? '' }}"
                            data-order="{{ $order }}">
                            <td>{{ $item['id_booking'] }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['wisata'] }}</td>
                            <td>{{ $item['tanggal_berangkat'] }}</td>
                            <td>{{ $item['jumlah_peserta'] }}</td>
                            {{-- ✅ KENDARAAN --}}
                            <td>
                                @if(!empty($item['kendaraan']))
                                    {{ $item['kendaraan'] }}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- ✅ STATUS BOOKING --}}
                            <td>
                                @if($item['status_booking'] == 'aktif')
                                    <span style="color:green;">Aktif</span>
                                @elseif($item['status_booking'] == 'pending')
                                    <span style="color:orange;">Pending</span>
                                @elseif($item['status_booking'] == 'batal')
                                    <span style="color:red;">Batal</span>
                                @else
                                    {{ ucfirst($item['status_booking']) }}
                                @endif
                            </td>

                            {{-- ✅ STATUS PEMBAYARAN --}}
                            <td>
                                @if($item['status_pembayaran'] == 'berhasil')
                                    <span style="color:green;">Berhasil</span>
                                @elseif($item['status_pembayaran'] == 'pending')
                                    <span style="color:orange;">Pending</span>
                                @elseif($item['status_pembayaran'] == 'expired')
                                    <span style="color:gray;">Expired</span>
                                @else
                                    <span style="color:red;">Gagal</span>
                                @endif
                            </td>
                            <td class="aksi-cell">
                                <a href="{{ route('booking.show', $item['id_booking']) }}" class="icon-action">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                               <a href="{{ route('booking.index', ['page' => 'edit', 'id' => $item['id_booking']]) }}" class="icon-action">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        @php $order++; @endphp
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;">Belum ada data booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- ================= HALAMAN EDIT BOOKING ================= --}}
    @if(($page ?? '') === 'edit' && $current)
        <div class="content-header">
            <div>
                <h1>Edit Data Booking</h1>
            </div>
        </div>

        <div class="main-scroll">
            <form method="POST" action="{{ route('booking.update', $current['id_booking']) }}">
                @csrf
                @method('PUT')

                <div class="form-container">
                    <div class="form-card">
                        <p class="form-id">ID Booking: {{ $current['id_booking'] }}</p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fa-solid fa-user"></i> Nama Lengkap</label>
                                <div class="input-box">{{ $current['nama'] }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-phone"></i> Nomor Telepon</label>
                                <div class="input-box">{{ $current['telepon'] }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-envelope"></i> Email</label>
                                <div class="input-box">{{ $current['email'] ?? '-' }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-house"></i> Alamat</label>
                                <div class="input-box">{{ $current['alamat'] ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3>Detail Perjalanan</h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Lokasi</label>
                                <div class="input-box">{{ $current['lokasi'] }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-cube"></i> Wisata</label>
                                <div class="input-box">{{ $current['wisata'] }}</div>
                            </div>

                            @if($current['tipe_booking'] === 'paket')
                                <div class="form-group">
                                    <label><i class="fa-regular fa-calendar"></i> Tanggal Pemberangkatan</label>
                                    <input type="date" name="tanggal_berangkat" class="form-select"
                                        value="{{ $current['tanggal_berangkat_raw'] }}">
                                </div>

                                <div class="form-group">
                                    <label><i class="fa-regular fa-calendar"></i> Tanggal Kembali</label>
                                    <input type="date" name="tanggal_kembali" class="form-select"
                                        value="{{ $current['tanggal_kembali_raw'] }}">
                                </div>
                            @else
                                <div class="form-group">
                                    <label><i class="fa-regular fa-calendar"></i> Tanggal Pemberangkatan</label>
                                    <div class="input-box">{{ $current['tanggal_berangkat'] }}</div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa-regular fa-calendar"></i> Tanggal Kembali</label>
                                    <div class="input-box">{{ $current['tanggal_kembali'] }}</div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label><i class="fa-solid fa-users"></i> Jumlah Peserta</label>
                                <div class="input-box">{{ $current['jumlah_peserta'] }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-map-location-dot"></i> Area / Kota Dilayani</label>
                                <div class="input-box">{{ $current['kota_layanan'] ?? '-' }}</div>
                            </div>

                            @if(($current['tipe_booking'] ?? '') === 'open_trip')
                                <div class="form-group">
                                    <label><i class="fa-solid fa-house"></i> Alamat Jemput</label>
                                    <div class="input-box">{{ $current['alamat_jemput'] ?? '-' }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="form-card">
                            <h3>Kendaraan</h3>

                            @if($current['tipe_booking'] === 'open_trip')
                                <p style="color:#6b7280; margin-bottom:10px;">
                                    Kendaraan open trip sudah ditentukan dari paket dan tidak bisa diubah.
                                </p>

                                <div class="input-box">
                                    {{ $current['kendaraan'] ?: '-' }}
                                </div>
                            @else
                                <p style="color:#6b7280; margin-bottom:10px;">
                                    Pilih kendaraan tambahan. Setiap kendaraan hanya bisa digunakan untuk satu booking pada tanggal
                                    yang sama.
                                </p>

                                <div style="display:flex; gap:10px; margin-bottom:14px;">
                                    <select id="selectKendaraan" class="form-select">
                                        <option value="">-- Pilih Kendaraan --</option>
                                        @foreach($allKendaraan as $kendaraan)
                                            @php
                                                $isDipakai = in_array($kendaraan->id_kendaraan, $kendaraanDipakaiIds ?? []);
                                            @endphp

                                            <option value="{{ $kendaraan->id_kendaraan }}"
                                                data-nama="{{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang" {{ $isDipakai ? 'disabled' : '' }}>
                                                {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                                                {{ $isDipakai ? '(Sudah dipakai)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="button" class="btn-save" onclick="tambahKendaraan()">
                                        Tambah
                                    </button>
                                </div>

                                <div id="selectedKendaraanList">
                                    @foreach($current['kendaraan_list'] as $k)
                                        <div class="selected-kendaraan-item" id="kendaraan-item-{{ $k->id_kendaraan }}">
                                            <span>{{ $k->nama_kendaraan }} - {{ $k->kapasitas }} orang</span>

                                            <button type="button" onclick="hapusKendaraan('{{ $k->id_kendaraan }}')"
                                                class="btn-remove-kendaraan">
                                                Hapus
                                            </button>

                                            <input type="hidden" name="kendaraan_checked[]" value="{{ $k->id_kendaraan }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-card">
                        <h3>Status dan Detail Pembayaran</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tanggal Booking</label>
                                <div class="input-box">{{ $current['tanggal_booking'] }}</div>
                            </div>

                            <div class="form-group">
                                <label>Status Booking</label>
                                <select name="status_booking" class="form-select">
                                    <option value="pending" {{ $current['status_booking'] == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="aktif" {{ $current['status_booking'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ $current['status_booking'] == 'selesai' ? 'selected' : '' }}>Selesai
                                    </option>
                                    <option value="batal" {{ $current['status_booking'] == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Harga Per Orang</label>
                                <div class="input-box">{{ $current['harga_per_orang'] }}</div>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Peserta</label>
                                <div class="input-box">{{ $current['jumlah_peserta'] }}</div>
                            </div>

                            <div class="form-group">
                                <label>Total Pembayaran</label>
                                <div class="input-box">{{ $current['total_pembayaran'] }}</div>
                            </div>

                            <div class="form-group">
                                <label>Tipe Pembayaran</label>
                                <div class="input-box">{{ strtoupper($current['tipe_pembayaran'] ?? '-') }}</div>
                            </div>

                            <div class="form-group">
                                <label>Opsi Pembayaran</label>
                                <div class="input-box">
                                    {{ ($current['opsi_pembayaran'] ?? '-') === 'dp' ? 'DP 25%' : 'Lunas' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Bayar</label>
                                <div class="input-box">{{ $current['jumlah_bayar'] ?? '-' }}</div>
                            </div>

                            <div class="form-group">
                                <label>Kode Pembayaran</label>
                                <div class="input-box">{{ $current['kode_pembayaran'] ?? '-' }}</div>
                            </div>

                            <div class="form-group">
                                <label>Status Pembayaran</label>
                                <select name="status_pembayaran" class="form-select">
                                    <option value="pending" {{ $current['status_pembayaran'] == 'pending' ? 'selected' : '' }}>
                                        Pending - Menunggu Konfirmasi
                                    </option>
                                    <option value="berhasil" {{ $current['status_pembayaran'] == 'berhasil' ? 'selected' : '' }}>
                                        Berhasil - Uang Sudah Diterima
                                    </option>
                                    <option value="gagal" {{ $current['status_pembayaran'] == 'gagal' ? 'selected' : '' }}>
                                        Gagal - Pembayaran Ditolak
                                    </option>
                                    <option value="expired" {{ ($current['status_pembayaran'] ?? '') == 'expired' ? 'selected' : '' }}>
                                        Expired - Pembayaran Kadaluarsa
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-action">
                        <a href="{{ route('booking.index') }}" class="btn-cancel">
                            Kembali
                        </a>
                        <button type="submit" class="btn-save">Ubah</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    {{-- ================= HALAMAN DETAIL BOOKING ================= --}}
    @if(($page ?? '') === 'detail' && $current)
        <div class="content-header">
            <div>
                <h1>Detail Data Booking</h1>
            </div>
        </div>

        <div class="main-scroll">
            <div class="form-container">
                <div class="form-card">
                    <p class="form-id">ID Booking: {{ $current['id_booking'] }}</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fa-solid fa-user"></i> Nama Lengkap</label>
                            <div class="input-box">{{ $current['nama'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-phone"></i> Nomor Telepon</label>
                            <div class="input-box">{{ $current['telepon'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-envelope"></i> Email</label>
                            <div class="input-box">{{ $current['email'] ?? '-' }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-house"></i> Alamat</label>
                            <div class="input-box">{{ $current['alamat'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Detail Perjalanan</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Lokasi</label>
                            <div class="input-box">{{ $current['lokasi'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-cube"></i> Wisata</label>
                            <div class="input-box">{{ $current['wisata'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-regular fa-calendar"></i> Tanggal Pemberangkatan</label>
                            <div class="input-box">{{ $current['tanggal_berangkat'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-regular fa-calendar"></i> Tanggal Kembali</label>
                            <div class="input-box">{{ $current['tanggal_kembali'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-users"></i> Jumlah Peserta</label>
                            <div class="input-box">{{ $current['jumlah_peserta'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Tipe Booking</label>
                            <div class="input-box">{{ $current['tipe_booking_label'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Area / Kota Dilayani</label>
                            <div class="input-box">{{ $current['kota_layanan'] ?? '-' }}</div>
                        </div>

                        @if(($current['tipe_booking'] ?? '') === 'open_trip')
                            <div class="form-group">
                                <label>Alamat Jemput</label>
                                <div class="input-box">{{ $current['alamat_jemput'] ?? '-' }}</div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Kendaraan</label>
                            <div class="input-box">{{ $current['kendaraan'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h3>Status dan Detail Pembayaran</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tanggal Booking</label>
                            <div class="input-box">{{ $current['tanggal_booking'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Status Booking</label>
                            <div class="input-box">{{ $current['status_booking_label'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Harga Per Orang</label>
                            <div class="input-box">{{ $current['harga_per_orang'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Peserta</label>
                            <div class="input-box">{{ $current['jumlah_peserta'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Total Pembayaran</label>
                            <div class="input-box">{{ $current['total_pembayaran'] }}</div>
                        </div>

                        <div class="form-group">
                            <label>Tipe Pembayaran</label>
                            <div class="input-box">{{ $current['tipe_pembayaran_label'] ?? '-' }}</div>
                        </div>

                        <div class="form-group">
                            <label>Opsi Pembayaran</label>
                            <div class="input-box">{{ $current['opsi_pembayaran_label'] ?? '-' }}</div>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Bayar</label>
                            <div class="input-box">{{ $current['jumlah_bayar'] ?? '-' }}</div>
                        </div>

                        <div class="form-group">
                            <label>Kode Pembayaran</label>
                            <div class="input-box">{{ $current['kode_pembayaran'] ?? '-' }}</div>
                        </div>

                        <div class="form-group">
                            <label>Status Pembayaran</label>
                            <div class="input-box">
                                @php
                                    $statusMap = [
                                        'pending' => 'Pending',
                                        'berhasil' => 'Berhasil',
                                        'gagal' => 'Gagal',
                                        'expired' => 'Expired',
                                    ];
                                @endphp
                                {{ $statusMap[$current['status_pembayaran']] ?? ucfirst($current['status_pembayaran'] ?? '-') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Refund</label>
                            <div class="input-box">
                                {{ !empty($current['jumlah_refund']) ? 'Rp ' . number_format($current['jumlah_refund'], 0, ',', '.') : '-' }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status Refund</label>
                            <div class="input-box">
                                {{ ucfirst(str_replace('_', ' ', $current['status_refund'] ?? 'tidak_ada')) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-action">
                    <a href="{{ route('booking.index') }}" class="btn-cancel">
                        Kembali
                    </a>
                    {{-- BATALKAN --}}
                    @if(!in_array($current['status_booking'], ['selesai', 'batal']))
                        <button type="button" class="btn-save" style="background:#ef4444;"
                            onclick="bukaModalBatal('{{ $current['id_booking'] }}')">
                            Batalkan Booking
                        </button>
                    @endif

                    {{-- REFUND --}}
                    @if(($current['status_refund'] ?? 'tidak_ada') === 'pending')
                        <form method="POST"
                            action="{{ route('dashboard.superadmin.kelola-data-booking.refund-selesai', $current['id_booking']) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-save" style="background:#16a34a;">
                                Tandai Refund Selesai
                            </button>
                        </form>
                    @endif

                    {{-- LUNASI --}}
                    @if($current['opsi_pembayaran'] === 'dp' && $current['status_booking'] === 'aktif' && ($current['sisa_bayar'] ?? 0) > 0)
                        <button type="button" class="btn-save" style="background:#16a34a;"
                            onclick="bukaModalLunasi('{{ $current['id_booking'] }}')">
                            Lunasi Sisa Pembayaran
                        </button>
                    @endif
                </div>

                <!-- Modal Batal Booking -->
                <div id="modalBatal"
                    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
                    <div style="background:#fff; border-radius:16px; padding:32px; max-width:420px; width:90%;">
                        <h3 style="margin-bottom:16px;">Konfirmasi Pembatalan</h3>
                        <p style="color:#6b7280; font-size:13px; margin-bottom:8px;">
                            Apakah Anda yakin ingin membatalkan booking ini?
                        </p>
                        <p style="color:#6b7280; font-size:13px; margin-bottom:20px;">
                            Estimasi refund (85%): <strong id="refundText">-</strong>
                        </p>
                        <div style="display:flex; gap:12px;">
                            <button type="button" class="btn-cancel" style="width:100%;"
                                onclick="tutupModalBatal()">Batal</button>
                            <button type="button" class="btn-save" style="background:#ef4444; width:100%;"
                                onclick="submitBatal()">Ya, Batalkan</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Pilih Metode Pelunasan --}}
            <div id="modalLunasi"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
                <div style="background:#fff; border-radius:16px; padding:32px; max-width:420px; width:90%;">
                    <h3 style="margin-bottom:16px;">Pilih Metode Pelunasan</h3>

                    <p style="color:#6b7280; font-size:13px; margin-bottom:20px;">
                        Sisa pembayaran: <strong id="sisaPelunasanText">-</strong>
                    </p>

                    <div style="display:flex; flex-direction:column; gap:12px;">
                        {{-- Cash --}}
                        <button type="button" class="btn-save" style="background:#16a34a; width:100%;"
                            onclick="konfirmasiLunasiCash()">
                            <i class="fa-solid fa-money-bill-wave"></i> Cash — Konfirmasi Uang Diterima
                        </button>

                        {{-- QRIS --}}
                        <button type="button" class="btn-save" style="background:#2563eb; width:100%;"
                            onclick="buatQrisPelunasan(currentLunasiId)">
                            <i class="fa-solid fa-qrcode"></i> QRIS — Generate QR Code
                        </button>

                        <button type="button" class="btn-cancel" style="width:100%;" onclick="tutupModalLunasi()">
                            Batal
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal QR Pelunasan --}}
            <div id="modalQrisPelunasan"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
                <div style="background:#fff; border-radius:16px; padding:32px; max-width:400px; width:90%; text-align:center;">
                    <h3 style="margin-bottom:8px;">QR Code Pelunasan</h3>
                    <p id="qrisSisaText" style="color:#6b7280; margin-bottom:16px;"></p>
                    <img id="qrisImage" src="" alt="QR Code"
                        style="width:220px; height:220px; object-fit:contain; margin-bottom:16px;">
                    <p style="font-size:12px; color:#6b7280;">Scan QR ini untuk melunasi sisa pembayaran</p>
                    <button onclick="tutupQris()" class="btn-cancel" style="margin-top:16px; width:100%;">Tutup</button>
                </div>
            </div>

            {{-- Form hidden untuk lunasi cash --}}
            <form id="formLunasiCash" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="metode_pelunasan" value="cash">
            </form>
        </div>
        </div>
    @endif

    {{-- ================= HALAMAN TAMBAH BOOKING MANUAL ================= --}}
    <div class="modal-overlay" id="modalTambahBooking" style="display:none;">
        <div class="form-container" style="max-width:600px; width:90%;">

            {{-- STEP 1: Form isi data --}}
            <div id="stepForm" class="form-card">
                <h3>Tambah Booking Manual</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Pelanggan</label>
                        <select name="id_users" id="modalUsers" class="form-select" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id_users }}">
                                    {{ $user->nama }} - {{ $user->no_hp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Paket Wisata</label>
                        <select name="id_paket" id="adminPaket" class="form-select" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($pakets as $paket)
                                @php
                                    $sisaKursi = $paket->sisa_kursi ?? 999;
                                    $isOpenTrip = $paket->tipe === 'open_trip';
                                    $habis = $isOpenTrip && $sisaKursi <= 0;
                                @endphp
                                <option value="{{ $paket->id_paket }}" data-tipe="{{ $paket->tipe }}"
                                    data-harga="{{ $paket->harga }}" data-nama="{{ $paket->nama_paket }}"
                                    data-min="{{ $paket->min_peserta ?? 1 }}" data-sisa="{{ $paket->sisa_kursi }}" {{ $habis ? 'disabled' : '' }}>
                                    {{ $paket->nama_paket }}
                                    ({{ $isOpenTrip ? 'Open Trip' : 'Paket' }})
                                    @if($isOpenTrip)
                                        — Sisa {{ $sisaKursi }} kursi
                                        @if($habis) (HABIS) @endif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Peserta</label>
                        <input type="number" id="modalJumlahPeserta" class="form-select" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Area / Kota Dilayani <span>*</span></label>
                        <select id="modalKotaLayanan" class="form-select" required>
                            <option value="">-- Pilih Area --</option>
                        </select>
                    </div>

                    <div class="form-group" id="wrapAlamatJemput" style="display:none;">
                        <label>Alamat Jemput <span>*</span></label>
                        <input type="text" id="modalAlamatJemput" class="form-select" placeholder="Alamat rumah / hotel">
                    </div>

                    <div class="form-group paket-only">
                        <label>Tanggal Berangkat</label>
                        <input type="date" id="modalTanggalBerangkat" class="form-select">
                    </div>

                    <div class="form-group paket-only">
                        <label>Tanggal Kembali</label>
                        <input type="date" id="modalTanggalKembali" class="form-select">
                    </div>

                    <div class="form-group paket-only" id="kendaraanWrapperModal">
                        <label>Kendaraan</label>
                        <p style="color:#6b7280; font-size:12px; margin-bottom:8px;">
                            Bisa pilih lebih dari 1 jika kapasitas tidak cukup.
                        </p>
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <select id="modalSelectKendaraan" class="form-select">
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($allKendaraan as $kendaraan)
                                    <option value="{{ $kendaraan->id_kendaraan }}"
                                        data-nama="{{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang"
                                        data-kapasitas="{{ $kendaraan->kapasitas }}"
                                        data-harga="{{ $kendaraan->harga_sewa ?? 0 }}">
                                        {{ $kendaraan->nama_kendaraan }} - {{ $kendaraan->kapasitas }} orang
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-save" onclick="tambahKendaraanModal()">Tambah</button>
                        </div>
                        <div id="modalSelectedKendaraan"></div>
                        <p style="font-size:12px; color:#6b7280; margin-top:6px;">
                            Total kapasitas: <span id="totalKapasitas">0</span> orang
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Tipe Pembayaran</label>
                        <select id="modalTipePembayaran" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Opsi Pembayaran</label>
                        <select id="modalOpsiPembayaran" class="form-select" required>
                            <option value="dp">DP 25%</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                </div>

                <div class="form-action">
                    <button type="button" class="btn-cancel" onclick="closeTambahBooking()">Batal</button>
                    <button type="button" class="btn-save" onclick="lihatRingkasan()">Lihat Ringkasan →</button>
                </div>
            </div>

            {{-- STEP 2: Ringkasan --}}
            <div id="stepRingkasan" class="form-card" style="display:none;">
                <h3>Ringkasan Booking</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Pelanggan</label>
                        <div class="input-box" id="rNama">-</div>
                    </div>
                    <div class="form-group">
                        <label>Paket</label>
                        <div class="input-box" id="rPaket">-</div>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Peserta</label>
                        <div class="input-box" id="rPeserta">-</div>
                    </div>
                    <div class="form-group">
                        <label>Tipe Pembayaran</label>
                        <div class="input-box" id="rTipe">-</div>
                    </div>
                    <div class="form-group">
                        <label>Opsi Pembayaran</label>
                        <div class="input-box" id="rOpsi">-</div>
                    </div>
                    <div class="form-group">
                        <label>Harga Per Orang</label>
                        <div class="input-box" id="rHarga">-</div>
                    </div>
                    <div class="form-group">
                        <label>Harga Sewa Kendaraan</label>
                        <div class="input-box" id="rSewa">-</div>
                    </div>
                    <div class="form-group">
                        <label>Total Biaya</label>
                        <div class="input-box" id="rTotal" style="font-weight:600; color:#1d4ed8;">-</div>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Bayar Sekarang</label>
                        <div class="input-box" id="rBayar" style="font-weight:600; color:#16a34a;">-</div>
                    </div>

                    {{-- Khusus cash: pilihan konfirmasi uang diterima --}}
                    <div class="form-group" id="cashKonfirmasiWrap" style="display:none;">
                        <label>Status Pembayaran Cash</label>
                        <select id="modalStatusCash" class="form-select">
                            <option value="pending">Pending - Belum Diterima</option>
                            <option value="berhasil">Uang Sudah Diterima</option>
                        </select>
                    </div>
                </div>

                <div class="form-action">
                    <button type="button" class="btn-cancel" onclick="kembaliKeForm()">← Kembali</button>
                    <button type="button" class="btn-save" onclick="submitBooking()">Konfirmasi & Simpan</button>
                </div>
            </div>

            {{-- Form hidden untuk submit --}}
            <form id="formBookingSubmit" method="POST" action="{{ route('booking.store') }}" style="display:none;">
                @csrf
                <input type="hidden" name="id_users" id="fUsers">
                <input type="hidden" name="id_paket" id="fPaket">
                <input type="hidden" name="jumlah_peserta" id="fPeserta">
                <input type="hidden" name="id_kota_layanan" id="fKotaLayanan">

                <input type="hidden" name="alamat_jemput" id="fAlamatJemput">
                <input type="hidden" name="tanggal_berangkat" id="fTanggalBerangkat">
                <input type="hidden" name="tanggal_kembali" id="fTanggalKembali">
                <input type="hidden" name="tipe_pembayaran" id="fTipe">
                <input type="hidden" name="opsi_pembayaran" id="fOpsi">
                <input type="hidden" name="status_pembayaran_awal" id="fStatusCash">
                <div id="fKendaraanContainer"></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        @php
            $paketData = $pakets->mapWithKeys(function ($paket) {
                return [
                    $paket->id_paket => [
                        'tipe' => $paket->tipe,
                        'kota_layanan' => $paket->kotaLayanan->map(function ($kota) {
                            return [
                                'id_kota' => $kota->id_kota,
                                'nama_kota' => $kota->nama_kota,
                            ];
                        })->values(),
                    ],
                ];
            });
        @endphp

        const paketData = @json($paketData);
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchBooking');
            const dateInput = document.getElementById('filterTanggal');
            const periodeSelect = document.getElementById('filterPeriode');
            const tipeSelect = document.getElementById('filterTipe');
            const jenisTanggalSelect = document.getElementById('filterJenisTanggal');
            const rows = Array.from(document.querySelectorAll('.booking-row'));

            // Kosongkan nilai default tanggal agar tidak auto-filter saat load
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.value = today;

                // penting: trigger filter langsung saat load
                setTimeout(() => {
                    filterRows();
                }, 0);
            }

            function filterRows() {
                const keyword = (searchInput?.value || '').toLowerCase().trim();
                const selectedDate = dateInput?.value || '';
                const selectedTipe = tipeSelect?.value || '';
                const periode = periodeSelect?.value || 'today';
                const jenisTanggal = jenisTanggalSelect?.value || 'transaksi'; // 'transaksi' | 'berangkat'

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                rows.forEach(row => {
                    const searchText = row.dataset.search || '';
                    const rowBerangkat = row.dataset.date || ''; // tanggal_sort = tanggal berangkat
                    const rowTransaksi = row.dataset.transaksi || ''; // tanggal booking/transaksi
                    const rowTipe = row.dataset.tipe || '';

                    // Pilih kolom tanggal sesuai jenis filter
                    const rowTanggal = jenisTanggal === 'berangkat' ? rowBerangkat : rowTransaksi;

                    // --- SEARCH ---
                    const matchSearch = keyword === '' || searchText.includes(keyword);

                    // --- TIPE ---
                    const matchTipe = selectedTipe === '' || rowTipe === selectedTipe;

                    // --- TANGGAL ---
                    let matchDate = true;

                    if (selectedDate !== '') {
                        // Filter tanggal manual: bandingkan dengan kolom yang sesuai
                        matchDate = rowTanggal === selectedDate;
                    } else {
                        // Filter periode
                        if (!rowTanggal) {
                            matchDate = periode === 'all';
                        } else {
                            const target = new Date(rowTanggal);
                            target.setHours(0, 0, 0, 0);

                            if (periode === 'today') {
                                matchDate = target.getTime() === today.getTime();

                            } else if (periode === 'week') {
                                const diffMs = today - target;
                                const diffDays = diffMs / (1000 * 60 * 60 * 24);
                                matchDate = diffDays >= 0 && diffDays <= 7;

                            } else if (periode === 'month') {
                                matchDate = target.getMonth() === today.getMonth() &&
                                    target.getFullYear() === today.getFullYear();

                            } else if (periode === 'last_month') {
                                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                                matchDate = target.getMonth() === lastMonth.getMonth() &&
                                    target.getFullYear() === lastMonth.getFullYear();

                            } else { // 'all'
                                matchDate = true;
                            }
                        }
                    }

                    row.style.display = (matchSearch && matchDate && matchTipe) ? '' : 'none';
                });
            }

            searchInput?.addEventListener('input', filterRows);
            tipeSelect?.addEventListener('change', filterRows);
            jenisTanggalSelect?.addEventListener('change', filterRows);
            dateInput?.addEventListener('change', filterRows);

            // Periode: reload ke server HANYA untuk mengambil data, filter tampilan tetap di JS
            periodeSelect?.addEventListener('change', filterRows);

            filterRows();
            // paksa refresh filter setelah DOM siap
            setTimeout(() => {
                filterRows();
            }, 50);
        });
        function openTambahBooking() {
            document.getElementById('modalTambahBooking').style.display = 'flex';
        }

        function closeTambahBooking() {
            document.getElementById('modalTambahBooking').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const paketSelect = document.getElementById('adminPaket');
            const paketOnlyFields = document.querySelectorAll('.paket-only');

            function togglePaketFields() {
                const selected = paketSelect.options[paketSelect.selectedIndex];
                const tipe = selected ? selected.dataset.tipe : '';

                paketOnlyFields.forEach(field => {
                    field.style.display = tipe === 'paket' ? '' : 'none';

                    field.querySelectorAll('input, select').forEach(input => {
                        input.required = tipe === 'paket';
                    });
                });
            }

            paketSelect?.addEventListener('change', togglePaketFields);
            togglePaketFields();
        });

        function tambahKendaraan() {
            const select = document.getElementById('selectKendaraan');
            const value = select.value;

            if (!value) {
                alert('Pilih kendaraan dulu.');
                return;
            }

            if (document.getElementById('kendaraan-item-' + value)) {
                alert('Kendaraan sudah ditambahkan.');
                return;
            }

            const selectedOption = select.options[select.selectedIndex];
            const nama = selectedOption.dataset.nama;

            const wrapper = document.createElement('div');
            wrapper.className = 'selected-kendaraan-item';
            wrapper.id = 'kendaraan-item-' + value;

            wrapper.innerHTML = `
            <span>${nama}</span>
            <button type="button" onclick="hapusKendaraan('${value}')" class="btn-remove-kendaraan">
                Hapus
            </button>
            <input type="hidden" name="kendaraan_checked[]" value="${value}">
        `;

            document.getElementById('selectedKendaraanList').appendChild(wrapper);

            select.value = '';
        }

        function hapusKendaraan(id) {
            const item = document.getElementById('kendaraan-item-' + id);

            if (item) {
                item.remove();
            }
        }
        // ===== KENDARAAN MODAL MULTI =====
        let modalKendaraanList = [];
        let semuaKendaraan = [];

        function fetchKendaraanTersedia() {
            const tglBerangkat = document.getElementById('modalTanggalBerangkat').value;
            const tglKembali = document.getElementById('modalTanggalKembali').value;
            const bookingId = document.getElementById('bookingId').value;

            if (!tglBerangkat || !tglKembali) return;

            fetch(`/dashboard/superadmin/kelola-data-booking/kendaraan-tersedia?tanggal_berangkat=${tglBerangkat}&tanggal_kembali=${tglKembali}&current_booking_id=${bookingId}`)
                .then(r => r.json())
                .then(data => {
                    semuaKendaraan = data;
                    renderSelectKendaraan();
                });
        }

        function renderSelectKendaraan() {
            const select = document.getElementById('modalSelectKendaraan');
            const selectedIds = modalKendaraanList.map(k => String(k.id));

            select.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';

            semuaKendaraan.forEach(k => {
                const sudahDipilih = selectedIds.includes(String(k.id_kendaraan));
                const option = document.createElement('option');
                option.value = k.id_kendaraan;
                option.dataset.nama = `${k.nama_kendaraan} - ${k.kapasitas} orang`;
                option.dataset.kapasitas = k.kapasitas;
                option.dataset.harga = k.harga_sewa ?? 0;
                option.disabled = k.dipakai || sudahDipilih;
                option.textContent = `${k.nama_kendaraan} - ${k.kapasitas} orang`
                    + (k.dipakai ? ' (Sudah dipakai)' : '')
                    + (sudahDipilih ? ' (Dipilih)' : '');
                select.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('modalTanggalBerangkat')?.addEventListener('change', fetchKendaraanTersedia);
            document.getElementById('modalTanggalKembali')?.addEventListener('change', fetchKendaraanTersedia);
        });

        function tambahKendaraanModal() {
            const select = document.getElementById('modalSelectKendaraan');
            const id = select.value;
            if (!id) { alert('Pilih kendaraan dulu.'); return; }
            if (modalKendaraanList.find(k => k.id == id)) { alert('Kendaraan sudah ditambahkan.'); return; }

            const opt = select.options[select.selectedIndex];
            modalKendaraanList.push({
                id,
                nama: opt.dataset.nama,
                kapasitas: parseInt(opt.dataset.kapasitas),
                harga: parseInt(opt.dataset.harga || 0),
            });
            renderModalKendaraan();
            renderSelectKendaraan(); // refresh dropdown
            select.value = '';
        }

        function hapusKendaraanModal(id) {
            modalKendaraanList = modalKendaraanList.filter(k => k.id != id);
            renderModalKendaraan();
            renderSelectKendaraan(); // refresh dropdown
        }

        function renderModalKendaraan() {
            const container = document.getElementById('modalSelectedKendaraan');
            const totalEl = document.getElementById('totalKapasitas');
            container.innerHTML = '';
            let total = 0;
            modalKendaraanList.forEach(k => {
                total += k.kapasitas;
                const div = document.createElement('div');
                div.className = 'selected-kendaraan-item';
                div.innerHTML = `
                <span>${k.nama}</span>
                <button type="button" onclick="hapusKendaraanModal('${k.id}')" class="btn-remove-kendaraan">Hapus</button>
            `;
                container.appendChild(div);
            });
            totalEl.textContent = total;
        }

        function formatRp(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        // ===== STEP 1 → STEP 2 =====
        function lihatRingkasan() {
            const paketSelect = document.getElementById('adminPaket');
            const paketOpt = paketSelect.options[paketSelect.selectedIndex];
            const tipe = paketOpt?.dataset.tipe || '';
            const harga = parseInt(paketOpt?.dataset.harga || 0);
            const namaPaket = paketOpt?.dataset.nama || '-';
            const jumlahPeserta = parseInt(document.getElementById('modalJumlahPeserta').value || 0);
            const tipePembayaran = document.getElementById('modalTipePembayaran').value;
            const opsiPembayaran = document.getElementById('modalOpsiPembayaran').value;

            const usersSelect = document.getElementById('modalUsers');
            const namaUser = usersSelect.options[usersSelect.selectedIndex]?.text || '-';

            // Validasi sisa kursi open trip
            const sisa = parseInt(paketOpt?.dataset.sisa || 999);
            if (tipe === 'open_trip' && jumlahPeserta > sisa) {
                alert(`Sisa kursi open trip hanya ${sisa} kursi, tidak cukup untuk ${jumlahPeserta} peserta.`);
                return;
            }

            // Validasi dasar
            if (!usersSelect.value) { alert('Pilih pelanggan.'); return; }
            if (!paketSelect.value) { alert('Pilih paket.'); return; }
            if (!jumlahPeserta || jumlahPeserta < 1) { alert('Isi jumlah peserta.'); return; }

            if (tipe === 'paket') {
                if (!document.getElementById('modalTanggalBerangkat').value ||
                    !document.getElementById('modalTanggalKembali').value) {
                    alert('Isi tanggal berangkat dan kembali.'); return;
                }
                if (modalKendaraanList.length === 0) {
                    alert('Pilih minimal 1 kendaraan.'); return;
                }
                const totalKapasitas = modalKendaraanList.reduce((s, k) => s + k.kapasitas, 0);
                if (totalKapasitas < jumlahPeserta) {
                    alert(`Kapasitas kendaraan (${totalKapasitas}) tidak cukup untuk ${jumlahPeserta} peserta.`);
                    return;
                }
            }

            // Hitung biaya
            const totalSewa = modalKendaraanList.reduce((s, k) => s + k.harga, 0);
            const totalBiaya = (harga * jumlahPeserta) + (tipe === 'paket' ? totalSewa : 0);
            const jumlahBayar = opsiPembayaran === 'dp' ? totalBiaya * 0.25 : totalBiaya;

            // Isi ringkasan
            document.getElementById('rNama').textContent = namaUser;
            document.getElementById('rPaket').textContent = namaPaket;
            document.getElementById('rPeserta').textContent = jumlahPeserta + ' Orang';
            document.getElementById('rTipe').textContent = tipePembayaran.toUpperCase();
            document.getElementById('rOpsi').textContent = opsiPembayaran === 'dp' ? 'DP 25%' : 'Lunas';
            document.getElementById('rHarga').textContent = formatRp(harga) + ' / orang';
            document.getElementById('rSewa').textContent = tipe === 'paket' ? formatRp(totalSewa) : '-';
            document.getElementById('rTotal').textContent = formatRp(totalBiaya);
            document.getElementById('rBayar').textContent = formatRp(jumlahBayar);

            // Tampilkan pilihan konfirmasi cash jika tipe cash
            const cashWrap = document.getElementById('cashKonfirmasiWrap');
            cashWrap.style.display = tipePembayaran === 'cash' ? '' : 'none';

            // Pindah ke step 2
            document.getElementById('stepForm').style.display = 'none';
            document.getElementById('stepRingkasan').style.display = '';
        }

        function kembaliKeForm() {
            document.getElementById('stepRingkasan').style.display = 'none';
            document.getElementById('stepForm').style.display = '';
        }

        function submitBooking() {
            const paketSelect = document.getElementById('adminPaket');
            const paketOpt = paketSelect.options[paketSelect.selectedIndex];
            const tipe = paketOpt?.dataset.tipe || '';
            const tipePembayaran = document.getElementById('modalTipePembayaran').value;
            const statusCash = document.getElementById('modalStatusCash').value;

            // Isi form hidden
            document.getElementById('fUsers').value = document.getElementById('modalUsers').value;
            document.getElementById('fPaket').value = paketSelect.value;
            document.getElementById('fPeserta').value = document.getElementById('modalJumlahPeserta').value;
            document.getElementById('fTanggalBerangkat').value = document.getElementById('modalTanggalBerangkat').value;
            document.getElementById('fTanggalKembali').value = document.getElementById('modalTanggalKembali').value;
            document.getElementById('fTipe').value = tipePembayaran;
            document.getElementById('fOpsi').value = document.getElementById('modalOpsiPembayaran').value;
            document.getElementById('fStatusCash').value = tipePembayaran === 'cash' ? statusCash : 'pending';
            document.getElementById('fKotaLayanan').value = document.getElementById('modalKotaLayanan').value;

            document.getElementById('fAlamatJemput').value = document.getElementById('modalAlamatJemput').value;

            // Isi kendaraan
            const fKendaraan = document.getElementById('fKendaraanContainer');
            fKendaraan.innerHTML = '';
            if (tipe === 'paket') {
                modalKendaraanList.forEach(k => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id_kendaraan[]';
                    input.value = k.id;
                    fKendaraan.appendChild(input);
                });
            }

            document.getElementById('formBookingSubmit').submit();
        }

        // ===== TOGGLE PAKET FIELDS =====
        document.addEventListener('DOMContentLoaded', function () {
            const paketSelect = document.getElementById('adminPaket');
            const paketOnlyFields = document.querySelectorAll('.paket-only');

            function togglePaketFields() {
                const opt = paketSelect?.options[paketSelect.selectedIndex];
                const tipe = opt ? opt.dataset.tipe : '';
                paketOnlyFields.forEach(field => {
                    field.style.display = tipe === 'paket' ? '' : 'none';
                });
            }

            paketSelect?.addEventListener('change', togglePaketFields);
            togglePaketFields();
        });

        // ===== MODAL OPEN/CLOSE =====
        function openTambahBooking() {
            document.getElementById('stepForm').style.display = '';
            document.getElementById('stepRingkasan').style.display = 'none';
            document.getElementById('modalTambahBooking').style.display = 'flex';
        }

        function closeTambahBooking() {
            document.getElementById('modalTambahBooking').style.display = 'none';
            modalKendaraanList = [];
            renderModalKendaraan();
        }

        // ===== PELUNASAN =====
        let currentLunasiId = null;
        let currentLunasiSisa = 0;

        function bukaModalLunasi(idBooking) {
            currentLunasiId = idBooking;

            // Ambil sisa dari data yang sudah ada di halaman
            fetch(`/dashboard/superadmin/kelola-data-booking/sisa/${idBooking}`)
                .then(r => r.json())
                .then(data => {
                    currentLunasiSisa = data.sisa;
                    document.getElementById('sisaPelunasanText').textContent = data.sisa_format;
                    document.getElementById('modalLunasi').style.display = 'flex';
                })
                .catch(() => {
                    // Fallback: langsung buka tanpa sisa
                    document.getElementById('sisaPelunasanText').textContent = '-';
                    document.getElementById('modalLunasi').style.display = 'flex';
                });
        }

        function tutupModalLunasi() {
            document.getElementById('modalLunasi').style.display = 'none';
            currentLunasiId = null;
        }

        function konfirmasiLunasiCash() {
            if (!confirm('Konfirmasi pelunasan cash? Pastikan uang sudah diterima.')) return;

            const form = document.getElementById('formLunasiCash');
            form.action = `/dashboard/superadmin/kelola-data-booking/${currentLunasiId}/lunasi`;
            form.submit();
        }

        function buatQrisPelunasan(idBooking) {
            tutupModalLunasi();
            document.getElementById('modalQrisPelunasan').style.display = 'flex';
            document.getElementById('qrisImage').src = '';
            document.getElementById('qrisSisaText').textContent = 'Memuat QR Code...';

            // Ambil CSRF dari form yang sudah ada di halaman (formLunasiCash)
            const csrfToken = document.querySelector('input[name="_token"]')?.value ?? '{{ csrf_token() }}';

            fetch(`/dashboard/superadmin/kelola-data-booking/qris-pelunasan/${idBooking}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                        tutupQris();
                        return;
                    }
                    document.getElementById('qrisSisaText').textContent = 'Sisa pembayaran: ' + data.sisa;
                    document.getElementById('qrisImage').src = data.qr_url;
                })
                .catch(err => {
                    alert('Gagal memuat QR Code: ' + err.message);
                    tutupQris();
                });
        }

        function tutupQris() {
            document.getElementById('modalQrisPelunasan').style.display = 'none';
        }

        let currentBatalId = null;

        function bukaModalBatal(idBooking) {
            currentBatalId = idBooking;

            fetch(`/dashboard/superadmin/kelola-data-booking/sisa/${idBooking}`)
                .then(r => r.json())
                .then(data => {
                    const sudahBayar = data.sudah_bayar || 0;
                    const totalBiaya = data.total_biaya || 0;

                    const isLunas = sudahBayar >= totalBiaya;

                    const refund = isLunas
                        ? Math.floor(sudahBayar * 0.85)
                        : 0;
                    const refundEl = document.getElementById('refundText');
                    if (refundEl) {
                        refundEl.textContent = refund > 0
                            ? 'Rp ' + refund.toLocaleString('id-ID')
                            : 'Tidak ada refund (DP hangus)';
                    }
                    document.getElementById('modalBatal').style.display = 'flex';
                })
                .catch(() => {
                    const refundEl = document.getElementById('refundText');
                    if (refundEl) refundEl.textContent = '-';
                    document.getElementById('modalBatal').style.display = 'flex';
                });
        }

        function tutupModalBatal() {
            document.getElementById('modalBatal').style.display = 'none';
        }

        function submitBatal() {
            if (!currentBatalId) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/dashboard/superadmin/kelola-data-booking/${currentBatalId}/batal`;

            form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('input[name=_token]').value}">
            <input type="hidden" name="_method" value="PATCH">
        `;

            document.body.appendChild(form);
            form.submit();
        }
        document.addEventListener('DOMContentLoaded', function () {
            const paketSelect = document.getElementById('adminPaket');
            const kotaSelect = document.getElementById('modalKotaLayanan');
            const wrapAlamat = document.getElementById('wrapAlamatJemput');
            const alamatInput = document.getElementById('modalAlamatJemput');

            function updateAreaJemput() {
                const idPaket = paketSelect.value;
                const data = paketData[idPaket];

                kotaSelect.innerHTML = '<option value="">-- Pilih Area --</option>';

                wrapAlamat.style.display = 'none';
                alamatInput.required = false;

                if (!data) return;

                data.kota_layanan.forEach(kota => {
                    kotaSelect.innerHTML += `<option value="${kota.id_kota}">${kota.nama_kota}</option>`;
                });

                if (data.tipe === 'open_trip') {
                    wrapAlamat.style.display = '';
                    alamatInput.required = true;
                }
            }

            paketSelect.addEventListener('change', updateAreaJemput);
            updateAreaJemput();
        });
    </script>
@endpush