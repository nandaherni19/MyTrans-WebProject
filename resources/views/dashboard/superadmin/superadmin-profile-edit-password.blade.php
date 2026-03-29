<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-profile.css') }}">
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">
                    <div class="brand-text">
                        <h2>MY Trans Nusa</h2>
                        <p>Super Admin</p>
                    </div>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="#">Dashboard</a>
                <a href="#">Kelola Paket Wisata dan Destinasi</a>
                <a href="{{ url('/superadmin/kelola-pengguna') }}">Kelola Pengguna</a>
                <a href="#">Kelola Kendaraan</a>
                <a href="#">Kelola Trayek</a>
                <a href="#">Data Booking</a>
                <a href="#">Laporan Transaksi</a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="{{ url('/superadmin/profile') }}" class="menu-profile">👤 Profil Saya</a>
            <a href="#" class="menu-logout">⛔ Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Profil Saya</h1>
            <a href="{{ url('/superadmin/profile') }}" class="btn-back">← Kembali</a>
        </div>

        <section class="profile-card">
            <div class="avatar-wrapper">
                <div class="avatar-circle">
                    @if(!empty($superadminProfile['photo']))
                        <img src="{{ asset($superadminProfile['photo']) }}" alt="Foto Profil Super Admin" class="avatar-image">
                    @else
                        <span class="avatar-icon">👤</span>
                    @endif
                </div>
            </div>

            <div class="profile-name">
                <h2>{{ $superadminProfile['name'] }}</h2>
            </div>
        </section>

        <section class="tab-section">
            <a href="{{ url('/superadmin/profile/edit') }}" class="tab-link">Ubah Informasi Pribadi</a>
            <a href="{{ url('/superadmin/profile/edit/password') }}" class="tab-link active">Ubah Password</a>
        </section>

        <section class="info-card">
            <div class="info-header">
                <h3>Ubah Password</h3>
            </div>

            <form action="{{ url('/superadmin/profile/password/update') }}" method="POST">
                @csrf

                <div class="password-group">
                    <label>Password Saat Ini</label>
                    <div class="password-input-wrapper">
                        <span class="password-left-icon">🔒</span>
                        <input type="password" id="currentPassword" name="current_password" value="{{ old('current_password') }}">
                        <button type="button" class="toggle-password" onclick="togglePassword('currentPassword', this)">👁️</button>
                    </div>
                    @error('current_password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="password-group">
                    <label>Password Baru</label>
                    <div class="password-input-wrapper">
                        <span class="password-left-icon">🔒</span>
                        <input type="password" id="newPassword" name="new_password" value="{{ old('new_password') }}">
                        <button type="button" class="toggle-password" onclick="togglePassword('newPassword', this)">👁️</button>
                    </div>
                    @error('new_password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="password-group">
                    <label>Konfirmasi Password Baru</label>
                    <div class="password-input-wrapper">
                        <span class="password-left-icon">🔒</span>
                        <input type="password" id="confirmPassword" name="confirm_password" value="{{ old('confirm_password') }}">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword', this)">👁️</button>
                    </div>
                    @error('confirm_password')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="edit-button-wrapper admin-edit-actions">
                    <a href="{{ url('/superadmin/profile') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}
</script>

</body>
</html>