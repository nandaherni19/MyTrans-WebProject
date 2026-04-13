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
        $bookingData = [
            'BK001' => [
                'id_booking' => 'BK001',
                'nama' => 'Gaelvino Arhan',
                'nama_tabel' => 'Gaelvino',
                'telepon' => '085664875559',
                'email' => 'gaelvino@gmail.com',
                'alamat' => 'Kebonsari, Madiun',
                'lokasi' => 'Pacitan',
                'wisata' => 'Paket Wisata Pantai Watu Karung',
                'tanggal_berangkat' => '17 Maret 2026',
                'tanggal_kembali' => '17 Maret 2026',
                'tanggal_booking' => '10 Maret 2026',
                'tanggal_sort' => '2026-03-17',
                'jumlah_peserta' => '20 Orang',
                'jumlah_peserta_tabel' => '20',
                'kendaraan' => 'Hiace',
                'status_booking' => 'menunggu',
                'status_booking_label' => 'Menunggu',
                'pembayaran_tabel' => 'DP',
                'status_pembayaran' => 'dp',
                'status_pembayaran_label' => 'Qris DP',
                'harga_per_orang' => 'Rp 1.500.000',
                'total_pembayaran' => 'Rp 30.000.000',
                'catatan' => '-',
            ],
            'BK002' => [
                'id_booking' => 'BK002',
                'nama' => 'Julian',
                'nama_tabel' => 'Julian',
                'telepon' => '081234567890',
                'email' => 'julian@gmail.com',
                'alamat' => 'Malang, Jawa Timur',
                'lokasi' => 'Lamongan',
                'wisata' => 'Request Trip WBL',
                'tanggal_berangkat' => '05 Mei 2026',
                'tanggal_kembali' => '05 Mei 2026',
                'tanggal_booking' => '01 Mei 2026',
                'tanggal_sort' => '2026-05-05',
                'jumlah_peserta' => '40 Orang',
                'jumlah_peserta_tabel' => '40',
                'kendaraan' => 'Bus',
                'status_booking' => 'diterima',
                'status_booking_label' => 'Diterima',
                'pembayaran_tabel' => 'Lunas',
                'status_pembayaran' => 'lunas',
                'status_pembayaran_label' => 'Lunas',
                'harga_per_orang' => 'Rp 500.000',
                'total_pembayaran' => 'Rp 20.000.000',
                'catatan' => '-',
            ],
            'BK003' => [
                'id_booking' => 'BK003',
                'nama' => 'Lokheswara',
                'nama_tabel' => 'Lokheswara',
                'telepon' => '089876543210',
                'email' => 'lokheswara@gmail.com',
                'alamat' => 'Malang, Jawa Timur',
                'lokasi' => 'Malang',
                'wisata' => 'Jatim Park II',
                'tanggal_berangkat' => '29 Mei 2026',
                'tanggal_kembali' => '29 Mei 2026',
                'tanggal_booking' => '25 Mei 2026',
                'tanggal_sort' => '2026-05-29',
                'jumlah_peserta' => '10 Orang',
                'jumlah_peserta_tabel' => '10',
                'kendaraan' => 'Hiace',
                'status_booking' => 'ditolak',
                'status_booking_label' => 'Ditolak',
                'pembayaran_tabel' => '-',
                'status_pembayaran' => 'belum_bayar',
                'status_pembayaran_label' => 'Belum Bayar',
                'harga_per_orang' => 'Rp 750.000',
                'total_pembayaran' => 'Rp 7.500.000',
                'catatan' => '-',
            ],
        ];

        $currentId = $id ?? 'BK001';
        $current = $bookingData[$currentId] ?? $bookingData['BK001'];
    @endphp

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= HALAMAN LIST ================= --}}
    @if(($page ?? 'index') === 'index')
        <div class="booking-topbar">
            <div class="booking-title">
                <h1>Data Booking</h1>
                <p>Kelola dan Konfirmasi Data Booking</p>
            </div>
        </div>

        <div class="booking-filter-wrap">
            <div class="booking-filter">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input
                        type="text"
                        id="searchBooking"
                        placeholder="Cari nama pelanggan"
                        style="width:100%; border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit;"
                    >
                </div>

                <div class="filter-dropdown">
                    <i class="fa-regular fa-calendar"></i>
                    <input
                        type="date"
                        id="filterTanggal"
                        style="border:none; outline:none; background:transparent; font-size:12px; color:#6b7280; font-family:inherit; cursor:pointer;"
                    >
                </div>
            </div>
        </div>

        <div class="booking-table-card">
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
                    @foreach($bookingData as $item)
                        <tr
                            class="booking-row"
                            data-name="{{ strtolower($item['nama_tabel']) }}"
                            data-date="{{ $item['tanggal_sort'] }}"
                            data-order="{{ $order }}"
                        >
                            <td>{{ $item['id_booking'] }}</td>
                            <td>{{ $item['nama_tabel'] }}</td>
                            <td>{{ $item['wisata'] }}</td>
                            <td>{{ $item['tanggal_berangkat'] }}</td>
                            <td>{{ $item['jumlah_peserta_tabel'] }}</td>
                            <td>{{ $item['kendaraan'] }}</td>
                            <td>{{ $item['status_booking_label'] }}</td>
                            <td>{{ $item['pembayaran_tabel'] }}</td>
                            <td class="aksi-cell">
                                <a href="{{ url()->current() }}?page=detail&id={{ $item['id_booking'] }}" class="icon-action">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ url()->current() }}?page=edit&id={{ $item['id_booking'] }}" class="icon-action">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        @php $order++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ================= HALAMAN EDIT BOOKING ================= --}}
    @if(($page ?? '') === 'edit')
        <div class="content-header">
            <div>
                <h1>Edit Data Booking</h1>
            </div>
        </div>

        <div class="main-scroll">
            <form method="POST" action="{{ route('dashboard.superadmin.kelola-data-booking.update', $current['id_booking']) }}">
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
                                <div class="input-box">{{ $current['email'] }}</div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa-solid fa-house"></i> Alamat</label>
                                <div class="input-box">{{ $current['alamat'] }}</div>
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
                        </div>
                    </div>

                    <div class="form-card">
                        <h3><i class="fa-solid fa-note-sticky"></i> Catatan</h3>
                        <div class="input-box">{{ $current['catatan'] }}</div>
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
                                    <option value="menunggu" {{ $current['status_booking'] === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="diterima" {{ $current['status_booking'] === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="ditolak" {{ $current['status_booking'] === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="selesai" {{ $current['status_booking'] === 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                                <label>Status Pembayaran</label>
                                <select name="status_pembayaran" class="form-select">
                                    <option value="belum_bayar" {{ $current['status_pembayaran'] === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="dp" {{ $current['status_pembayaran'] === 'dp' ? 'selected' : '' }}>DP</option>
                                    <option value="lunas" {{ $current['status_pembayaran'] === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-action">
                        <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}" class="btn-cancel">Kembali</a>
                        <button type="submit" class="btn-save">Ubah</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    {{-- ================= HALAMAN DETAIL BOOKING ================= --}}
    @if(($page ?? '') === 'detail')
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
                            <div class="input-box">{{ $current['email'] }}</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa-solid fa-house"></i> Alamat</label>
                            <div class="input-box">{{ $current['alamat'] }}</div>
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
                    </div>
                </div>

                <div class="form-card">
                    <h3><i class="fa-solid fa-note-sticky"></i> Catatan</h3>
                    <div class="input-box">{{ $current['catatan'] }}</div>
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
                            <label>Status Pembayaran</label>
                            <div class="input-box">{{ $current['status_pembayaran_label'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-action">
                    <a href="{{ route('dashboard.superadmin.kelola-data-booking') }}" class="btn-cancel">Kembali</a>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchBooking');
    const dateInput = document.getElementById('filterTanggal');
    const tbody = document.getElementById('bookingTableBody');
    const rows = Array.from(document.querySelectorAll('.booking-row'));

    if (!tbody || rows.length === 0) return;

    function sortRows() {
        const keyword = (searchInput?.value || '').toLowerCase().trim();
        const selectedDate = dateInput?.value || '';

        const sortedRows = [...rows].sort((a, b) => {
            const aName = a.dataset.name;
            const bName = b.dataset.name;
            const aDate = a.dataset.date;
            const bDate = b.dataset.date;
            const aOrder = parseInt(a.dataset.order, 10);
            const bOrder = parseInt(b.dataset.order, 10);

            const aSearchMatch = keyword !== '' && aName.includes(keyword) ? 1 : 0;
            const bSearchMatch = keyword !== '' && bName.includes(keyword) ? 1 : 0;

            const aDateMatch = selectedDate !== '' && aDate === selectedDate ? 1 : 0;
            const bDateMatch = selectedDate !== '' && bDate === selectedDate ? 1 : 0;

            if (aSearchMatch !== bSearchMatch) {
                return bSearchMatch - aSearchMatch;
            }

            if (aDateMatch !== bDateMatch) {
                return bDateMatch - aDateMatch;
            }

            return aOrder - bOrder;
        });

        sortedRows.forEach(row => tbody.appendChild(row));
    }

    searchInput?.addEventListener('input', sortRows);
    dateInput?.addEventListener('change', sortRows);
});
</script>
@endpush