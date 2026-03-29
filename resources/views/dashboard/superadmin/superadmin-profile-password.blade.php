<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Super Admin</title>
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
                <a href="{{ url('/superadmin/kelola-pengguna') }}">Kelola Pengguna</a>
                <a href="#">Kelola Paket Wisata dan Destinasi</a>
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

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

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
            <a href="{{ url('/superadmin/profile') }}" class="tab-link">Informasi Pribadi</a>
            <a href="{{ url('/superadmin/profile/password') }}" class="tab-link active">Password</a>
        </section>

        <section class="info-card admin-password-view-card">
            <div class="info-header">
                <h3>Ubah Password</h3>
            </div>

            <div class="password-group">
                <label>Password Saat Ini</label>
                <div class="password-input-wrapper">
                    <span class="password-left-icon">🔒</span>
                    <input type="password" id="superadminSavedPassword" value="{{ $superadminPassword['current_password'] }}" readonly>
                    <button type="button" class="toggle-password" onclick="togglePassword('superadminSavedPassword', this)">👁️</button>
                </div>
            </div>
        </section>

        <section class="security-tip-card">
            <div class="security-icon">🛡️</div>
            <div class="security-text">
                <h4>Tips Keamanan</h4>
                <p>Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak membagikan informasi login Anda kepada siapapun.</p>
            </div>
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