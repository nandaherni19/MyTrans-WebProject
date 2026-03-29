<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/admin/superadmin-kelolapengguna.css') }}">
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="brand-logo">
                    <div class="brand-text">
                        <h2>MY Trans Nusa</h2>
                        <p>Super admin</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="{{ route('dashboard.admin') }}">Dashboard</a>
                <a href="{{ route('dashboard.superadmin.kelola-pengguna') }}" class="active">Kelola pengguna</a>
                <a href="{{ route('dashboard.superadmin.kelola-paket-wisata') }}">Kelola paket wisata dan destinasi</a>
                <a href="{{ route('dashboard.superadmin.request-booking') }}">Request Booking</a>
                <a href="{{ route('dashboard.superadmin.kelola-kendaraan') }}">Kelola Kendaraan</a>
                <a href="{{ route('dashboard.superadmin.kelola-trayek') }}">Kelola Trayek</a>
                <a href="{{ route('dashboard.superadmin.data-booking') }}">Data Booking</a>
                <a href="{{ route('dashboard.superadmin.laporan-transaksi') }}">Laporan Transaksi</a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ route('dashboard.superadmin.profile') }}" class="menu-profile"><i class="fa-solid fa-user"></i> Profil Saya</a>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#ff2800;cursor:pointer;">
                <i class="fa-solid fa-circle-minus"></i> Logout
            </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Kelola Pengguna</h1>
            <button class="btn-add" onclick="openTambah()">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <section class="user-card">
            <div class="user-card-header">
                <h3>Daftar Pengguna</h3>
            </div>

            <div class="table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No Hp</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                    <td>{{ $user->nama }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->no_hp }}</td>
                    <td><span class="role-badge">{{ $user->role }}</span></td>
                    <td class="action-cell">
                        <button class="edit-icon" onclick="openEdit(
                            '{{ $user->id_users }}',
                            '{{ $user->nama }}',
                            '{{ $user->email }}',
                            '{{ $user->no_hp }}',
                            '{{ $user->role }}')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="delete-icon" onclick="openHapus('{{ $user->id_users }}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- tambah -->
<div id="modalTambah" class="overlay">
    <div class="modal-box">
        <h2>Tambah Pengguna</h2>

        <form method="POST" action="{{ route('dashboard.superadmin.kelola-pengguna.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>No Hp</label>
                <input type="text" name="no_hp" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" required>
                    <span class="toggle-password material-symbols-outlined"
                        onclick="togglePassword(this)">visibility</span>
                </div>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirmation" required>
                    <span class="toggle-password material-symbols-outlined"
                        onclick="togglePassword(this)">visibility</span>
                </div>
                @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option>admin</option>
                    <option>superadmin</option>
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn-kembali" onclick="closeTambah()">Kembali</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
    @if($errors->any())
    <div style="color:red">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
</div>


<!-- edit -->
<div id="modalEdit" class="overlay">
    <div class="modal-box">
        <h2>Edit Pengguna</h2>

        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" id="editNama" name="nama">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="editEmail" name="email">
            </div>

            <div class="form-group">
                <label>No Hp</label>
                <input type="text" id="editHp" name="no_hp">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select id="editRole" name="role">
                    <option>admin</option>
                    <option>superadmin</option>
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="btn-kembali" onclick="closeEdit()">Kembali</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- hapus -->
<div id="modalHapus" class="overlay">
    <div class="delete-box">
        <div class="delete-header">
            <div class="warning-icon">!</div>
            <h2>Hapus Pengguna</h2>
        </div>

        <div class="delete-body">
            <h3>Apakah Anda yakin ingin menghapus pengguna ini?</h3>
            <p>Data pengguna yang dihapus tidak dapat dikembalikan.</p>
        </div>

        <div class="button-group">
            <button class="btn-batal" onclick="closeHapus()">Batal</button>
            <form id="formHapus" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-hapus">Hapus Pengguna</button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(icon) {
    const input = icon.previousElementSibling;

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "visibility_off";
    } else {
        input.type = "password";
        icon.textContent = "visibility";
    }
}

function openTambah() {
    document.getElementById('modalTambah').style.display = 'flex';
}

function closeTambah() {
    document.getElementById('modalTambah').style.display = 'none';
}

function openEdit(id, nama, email, hp, role) {
    document.getElementById('editNama').value = nama;
    document.getElementById('editEmail').value = email;
    document.getElementById('editHp').value = hp;
    document.getElementById('editRole').value = role;

    document.getElementById('formEdit').action =
        '/dashboard/superadmin/kelola-pengguna/update/' + id;

    document.getElementById('modalEdit').style.display = 'flex';
}

function closeEdit() {
    document.getElementById('modalEdit').style.display = 'none';
}
function openHapus(id) {
    document.getElementById('formHapus').action =
        '/dashboard/superadmin/kelola-pengguna/delete/' + id;

    document.getElementById('modalHapus').style.display = 'flex';
}

function closeHapus() {
    document.getElementById('modalHapus').style.display = 'none';
}

// klik luar modal
window.onclick = function(event) {
    let tambah = document.getElementById('modalTambah');
    let edit = document.getElementById('modalEdit');
    let hapus = document.getElementById('modalHapus');

    if (event.target === tambah) tambah.style.display = "none";
    if (event.target === edit) edit.style.display = "none";
    if (event.target === hapus) hapus.style.display = "none";
}
</script>

</body>
</html>