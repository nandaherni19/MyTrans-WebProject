<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <nav class="topbar-nav">
        <a href="#">Beranda</a>
        <span>|</span>
        <a href="#">Paket Wisata</a>
        <span>|</span>
        <a href="#">Booking</a>
        <span>|</span>
        <a href="#">Riwayat Booking</a>
        <span>|</span>
        <a href="{{ url('/profile') }}" class="active">Profil</a>
    </nav>

    <div class="topbar-right">
        <a href="{{ url('/profile') }}" class="btn-back">← Kembali</a>
    </div>
</header>

<main class="profile-page">
    <section class="page-header">
        <h1>Profil Saya</h1>
        <p>Kelola informasi profil Anda dengan mudah</p>
    </section>

    <section class="profile-hero-card">
        <div class="profile-hero-left">
            <div class="avatar-wrapper">
                <div class="avatar-circle">
    @if(!empty($profile['photo']))
        <img src="{{ asset($profile['photo']) }}" alt="Foto Profil" class="avatar-image">
    @else
        <span class="avatar-icon">👤</span>
    @endif
</div>
            </div>
        </div>

        <div class="profile-hero-right">
            <h2>{{ $profile['name'] }}</h2>
        </div>
    </section>

    <section class="profile-tabs">
        <a href="{{ url('/profile/edit') }}" class="tab-btn">Ubah Informasi Pribadi</a>
        <a href="{{ url('/profile/edit/password') }}" class="tab-btn active">Ubah Password</a>
    </section>

    <section class="profile-info-card">
        <div class="info-title">
            <h3>Ubah Password</h3>
            <p>Perbarui keamanan akun Anda</p>
        </div>

        <form action="{{ url('/profile/password/update') }}" method="POST">
            @csrf

            <div class="password-form-group">
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

            <div class="password-form-group">
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

            <div class="password-form-group">
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

            <div class="edit-action-wrapper">
                <a href="{{ url('/profile/password') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            </div>
        </form>
    </section>

    <section class="security-tip-card">
        <div class="security-icon">🛡️</div>
        <div class="security-text">
            <h4>Tips Keamanan</h4>
            <p>
                Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak membagikan informasi login Anda kepada siapapun.
            </p>
        </div>
    </section>
</main>

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