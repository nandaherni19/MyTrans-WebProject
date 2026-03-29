<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Saya</title>
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

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- HERO -->
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

    <!-- TAB -->
    <section class="profile-tabs">
        <a href="{{ url('/profile') }}" class="tab-btn">Informasi Pribadi</a>
        <a href="{{ url('/profile/password') }}" class="tab-btn active">Password</a>
    </section>

    <!-- PASSWORD CARD -->
    <section class="profile-info-card">
        <div class="info-title">
            <h3>Password</h3>
            <p>Keamanan akun Anda</p>
        </div>

        <div class="info-grid">
            <div class="info-item full-width-left">
                <label>Password Saat Ini</label>

                <div class="password-input-wrapper">
                    <span class="password-left-icon">🔒</span>
                    <input type="password" id="savedPassword"
                           value="{{ $passwordData['current_password'] }}" readonly>
                    <button type="button" class="toggle-password"
                            onclick="togglePassword('savedPassword', this)">👁️</button>
                </div>
            </div>
        </div>
    </section>

    <!-- SECURITY TIP -->
    <section class="security-tip-card">
        <div class="security-icon">🛡️</div>
        <div class="security-text">
            <h4>Tips Keamanan</h4>
            <p>
                Untuk keamanan akun Anda, pastikan menggunakan password yang kuat dan tidak
                membagikan informasi login Anda kepada siapapun.
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