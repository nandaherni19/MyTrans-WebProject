@extends('layouts.admin')
@section('title', 'Kelola Kendaraan')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/kelola-kendaraan.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@endpush

@section('content')
    <div class="kendaraan-topbar">
        <div class="kendaraan-title">
            <h1>Kelola Kendaraan</h1>
            <p>Kelola dan Konfirmasi Kendaraan</p>
        </div>

        <div class="header-actions">
            <button type="button" class="btn-primary" onclick="openTambah()">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kendaraan</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="main-scroll">
        <section class="kendaraan-wrapper">
            <div class="kendaraan-grid">

                @forelse ($kendaraans as $k)
                        <div class="kendaraan-card">
                            <img src="{{ asset('storage/' . $k->foto_kendaraan) }}" alt="{{ $k->nama_kendaraan }}">

                            <div class="kendaraan-body">
                                <div class="kendaraan-status">{{ $k->status_kendaraan }}</div>
                                <h3>{{ $k->nama_kendaraan }}</h3>
                                <p class="kendaraan-penumpang">👤 {{ $k->kapasitas }} Penumpang</p>
                                <!-- <p class="kendaraan-plat">Plat Nomor {{ $k->plat_nomor }}</p> -->
                                <div class="kendaraan-divider"></div>
                                <div class="kendaraan-price-row">
                                    <p class="kendaraan-price-label">Mulai dari</p>

                                    <div class="kendaraan-price-box">
                                        <h4 class="kendaraan-price">
                                            Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}
                                        </h4>
                                        <span class="kendaraan-price-unit">per hari</span>
                                    </div>
                                </div>

                                <div class="kendaraan-actions">
                                    <button type="button" class="btn-edit" onclick="openEdit(
                        '{{ $k->id_kendaraan }}',
                        '{{ $k->nama_kendaraan }}',
                        '{{ $k->jenis_kendaraan }}',
                        '{{ $k->kapasitas }}',
                        // '{{ $k->plat_nomor }}',
                        '{{ $k->status_kendaraan }}',
                        '{{ $k->harga_sewa }}'
                    )">
                                        <i class="fa-solid fa-pen"></i>
                                        <span>Edit</span>
                                    </button>

                                    <button type="button" class="btn-delete" onclick="openHapus('{{ $k->id_kendaraan }}')">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                @empty
                    <div class="empty-box">
                        <h3>Belum ada data kendaraan</h3>
                        <p>Silakan tambahkan data terlebih dahulu.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
    <div class="modal-overlay" id="modalTambah">
        <div class="tambah-modal">
            <h2 class="tambah-title">Tambah Kendaraan</h2>

            @if($errors->any())
                <div style="color:red; margin-bottom:10px;">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('dashboard.superadmin.kelola-kendaraan.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="tambah-grid">

                    <div class="tambah-group">
                        <label>Nama Kendaraan <span>*</span></label>
                        <input type="text" name="nama_kendaraan" placeholder="Toyota Hiace" required>
                    </div>

                    <div class="tambah-group">
                        <label>Jenis Kendaraan <span>*</span></label>
                        <select name="jenis_kendaraan" required>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                            <option value="mobil">Mobil</option>
                            <option value="hiace">Hiace</option>
                            <option value="elf">Minibus</option>
                            <option value="bus">Bus</option>
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Kapasitas <span>*</span></label>
                        <input type="number" name="kapasitas" min="6" required>
                    </div>

                    <!-- <div class="tambah-group">
                        <label>Plat Nomor <span>*</span></label>
                        <input type="text" name="plat_nomor" placeholder="N 7144 AB" required>
                    </div> -->

                    <div class="tambah-group">
                        <label>Status <span>*</span></label>
                        <select id="tambahStatus" name="status_kendaraan" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="tersedia">Tersedia</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Harga per hari <span>*</span></label>
                        <input type="number" name="harga_sewa" min="0" placeholder="170000" required>
                    </div>

                    <div class="tambah-group">
                        <label>Gambar <span>*</span></label>
                        <input type="file" name="foto_kendaraan" accept="image/*" required>
                    </div>

                </div>

                <div class="tambah-actions">
                    <button type="button" class="btn-modal-secondary" onclick="closeTambah()">Kembali</button>
                    <button type="submit" class="btn-modal-primary">Simpan</button>
                </div>
            </form>
        </div>
        @if ($errors->any())
            <div style="color:red">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>

    <div class="modal-overlay" id="modalEdit">
        <div class="tambah-modal">
            <h2 class="tambah-title">Edit Kendaraan</h2>

            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="tambah-grid">
                    <div class="tambah-group">
                        <label>Nama Kendaraan <span>*</span></label>
                        <input type="text" id="editNama" name="nama_kendaraan" placeholder="Toyota Hiace">
                    </div>

                    <div class="tambah-group">
                        <label>Jenis Kendaraan <span>*</span></label>
                        <select id="editJenis" name="jenis_kendaraan" required>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                            <option value="mobil">Mobil</option>
                            <option value="hiace">Hiace</option>
                            <option value="elf">Minibus</option>
                            <option value="bus">Bus</option>
                        </select>
                    </div>

                    <div class="tambah-group">
                        <label>Kapasitas <span>*</span></label>
                        <input type="number" id="editKapasitas" name="kapasitas" min="1">
                    </div>

                    <div class="tambah-group">
                        <label>Status <span>*</span></label>
                        <select id="editStatus" name="status_kendaraan" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="tersedia">Tersedia</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <!-- <div class="tambah-group">
                        <label>Plat Nomor <span>*</span></label>
                        <input type="text" id="editTrayek" name="plat_nomor" placeholder="N 7144 AB">
                    </div> -->

                    <div class="tambah-group">
                        <label>Harga per hari <span>*</span></label>
                        <input type="number" id="editHarga" name="harga_sewa" placeholder="RP 1.700.000">
                    </div>

                    <div class="tambah-group">
                        <label>Gambar</label>
                        <input type="file" id="editGambar" name="foto_kendaraan">
                    </div>
                </div>
                <div class="tambah-actions">
                    <button type="button" class="btn-modal-secondary" onclick="closeEdit()">Kembali</button>
                    <button type="submit" class="btn-modal-primary">Ubah</button>
                </div>
            </form>
        </div>
    </div>


    <div class="delete-modal-overlay" id="modalHapus">
        <div class="delete-modal-box">
            <div class="delete-header">
                <div class="warning-icon">!</div>
                <h2>Hapus Kendaraan</h2>
            </div>

            <div class="delete-body">
                <h3>Apakah anda ingin menghapus kendaraan ini?</h3>
                <p>Data kendaraan yang dihapus tidak dapat dikembalikan.</p>
            </div>

            <div class="delete-actions">
                <button type="button" class="btn-cancel-delete" onclick="closeHapus()">Batal</button>

                <form id="formHapus" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Hapus Kendaraan</button>
                </form>
            </div>
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

        function openEdit(id, nama_kendaraan, jenis_kendaraan, kapasitas, status_kendaraan, harga_sewa) {
            document.getElementById('editNama').value = nama_kendaraan;
            document.getElementById('editJenis').value = jenis_kendaraan;
            document.getElementById('editKapasitas').value = kapasitas;
            // document.getElementById('editTrayek').value = plat_nomor;
            document.getElementById('editStatus').value = status_kendaraan;
            document.getElementById('editHarga').value = harga_sewa;

            document.getElementById('formEdit').action =
                '/dashboard/superadmin/kelola-kendaraan/update/' + id;

            document.getElementById('modalEdit').classList.add('show');
        }

        function closeEdit() {
            document.getElementById('modalEdit').classList.remove('show');
        }

        function openHapus(id) {
            document.getElementById('formHapus').action =
                '/dashboard/superadmin/kelola-kendaraan/delete/' + id;

            document.getElementById('modalHapus').classList.add('show');
        }

        function closeHapus() {
            document.getElementById('modalHapus').classList.remove('show');
        }
        // ── Kompres Foto Kendaraan ─────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            const fotoInputs = document.querySelectorAll('input[name="foto_kendaraan"]');

            fotoInputs.forEach(function (fotoInput) {

                fotoInput.addEventListener('change', function (e) {

                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function (event) {

                        const img = new Image();
                        img.onload = function () {

                            let width  = img.width;
                            let height = img.height;
                            const max  = 800;

                            if (width > max || height > max) {
                                if (width > height) {
                                    height = Math.round(height * max / width);
                                    width  = max;
                                } else {
                                    width  = Math.round(width * max / height);
                                    height = max;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width  = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob(function (blob) {

                                const compressedFile = new File(
                                    [blob],
                                    'foto_kendaraan.jpg',
                                    { type: 'image/jpeg' }
                                );

                                const dt = new DataTransfer();
                                dt.items.add(compressedFile);
                                fotoInput.files = dt.files;

                            }, 'image/jpeg', 0.7);
                        };

                        img.src = event.target.result;
                    };

                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
@endpush