@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/kelola-pengguna.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
@endpush

@section('content')
    <div class="content-header">
        <div>
            <h1>Kelola Pengguna</h1>
            <p>Kelola Pengguna dan Perbarui Data Pengguna</p>
        </div>

        <div class="header-actions">
            <form method="GET" class="filter-form">

                <input type="text" name="search" placeholder="Cari nama / email..." value="{{ request('search') }}">

                <select name="role">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                </select>

                <button type="submit">Filter</button>
            </form>

            <button class="btn-add" onclick="openTambah()">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="main-scroll">
        <section class="user-card">
            <h3>Daftar Pengguna</h3>

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
                    @foreach($users as $ms_user)
                        <tr>
                            <td data-label="Nama">{{ $ms_user->nama }}</td>
                            <td data-label="Email">{{ $ms_user->email }}</td>
                            <td data-label="No Hp">{{ $ms_user->no_hp }}</td>
                            <td data-label="Role"><span class="role-badge">{{ $ms_user->role }}</span></td>
                            <td data-label="Aksi" class="action-cell">
                                <div class="aksi-wrapper">
                                    <button onclick="openEdit(
                                    '{{ $ms_user->id_users }}',
                                    '{{ $ms_user->nama }}',
                                    '{{ $ms_user->email }}',
                                    '{{ $ms_user->no_hp }}',
                                    '{{ $ms_user->role }}'
                                )" class="btn-edit">
                                        <i class="fa-solid fa-pen"></i>
                                        <span>Edit</span></button>
                                    <button onclick="openHapus('{{ $ms_user->id_users }}')" class="btn-delete">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <!-- tambah -->
        <div id="modalTambah" class="overlay">
            <div class="modal-box">
                <h2>Tambah Pengguna</h2>

                {{-- TAMBAHKAN INI --}}
                @if($errors->any())
                    <div style="background:#fee2e2; color:#dc2626; padding:10px; border-radius:6px; margin-bottom:12px;">
                        @foreach($errors->all() as $error)
                            <p style="margin:2px 0;">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

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
                        <select id="editRole" name="role" disabled>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                            <option value="user">User</option>
                        </select>
                        <input type="hidden" id="editRoleHidden" name="role">
                    </div>

                    <div id="passwordFields">
                        <div class="form-group">
                            <label>Password Baru</label>
                            <div class="password-wrapper">
                                <input type="password" id="editPassword" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                <span class="toggle-password material-symbols-outlined"
                                    onclick="togglePassword(this)">visibility</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <div class="password-wrapper">
                                <input type="password" id="editPasswordConfirmation" name="password_confirmation"
                                    placeholder="Ulangi password baru">
                                <span class="toggle-password material-symbols-outlined"
                                    onclick="togglePassword(this)">visibility</span>
                            </div>
                        </div>
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
                    <h3>Apakah anda ingin menghapus pengguna ini?</h3>
                    <p>Data pengguna yang dihapus tidak dapat dikembalikan.</p>
                </div>

                <div class="button-group delete-buttons">
                    <button class="btn-batal" onclick="closeHapus()">Batal</button>
                    <form id="formHapus" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus">Hapus Pengguna</button>
                    </form>
                </div>
            </div>
        </div>
@endsection

    @push('scripts')
        <script>
            @if($errors->any() && old('nama'))
                document.addEventListener('DOMContentLoaded', function () {
                    openTambah();
                });
            @endif

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
                document.getElementById('modalTambah').classList.add('show');
            }

            function closeTambah() {
                document.getElementById('modalTambah').classList.remove('show');
            }

            function openEdit(id, nama, email, hp, role) {
                document.getElementById('editNama').value = nama;
                document.getElementById('editEmail').value = email;
                document.getElementById('editHp').value = hp;
                document.getElementById('editRole').value = role;
                document.getElementById('editRoleHidden').value = role;

                document.getElementById('formEdit').action =
                    '/dashboard/superadmin/kelola-pengguna/update/' + id;

                const passwordFields = document.getElementById('passwordFields');
                const editPassword = document.getElementById('editPassword');
                const editPasswordConfirmation = document.getElementById('editPasswordConfirmation');

                if (role === 'admin' || role === 'superadmin') {
                    passwordFields.style.display = 'block';
                } else {
                    passwordFields.style.display = 'none';
                    editPassword.value = '';
                    editPasswordConfirmation.value = '';
                }

                document.getElementById('modalEdit').classList.add('show');
            }

            function closeEdit() {
                document.getElementById('modalEdit').classList.remove('show');
            }

            function openHapus(id) {
                document.getElementById('formHapus').action =
                    '/dashboard/superadmin/kelola-pengguna/delete/' + id;

                document.getElementById('modalHapus').classList.add('show');
            }

            function closeHapus() {
                document.getElementById('modalHapus').classList.remove('show');
            }
        </script>
    @endpush