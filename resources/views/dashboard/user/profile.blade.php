<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
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
            <a href="{{ url('/') }}" class="btn-back">← Kembali</a>
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
            <a href="{{ url('/profile') }}" class="tab-btn active">Informasi Pribadi</a>
            <a href="{{ url('/profile/password') }}" class="tab-btn">Password</a>
        </section>

        <section class="profile-info-card">
            <div class="info-title">
                <h3>Profil</h3>
                <p>Identitas Anda</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <div class="info-box">{{ $profile['name'] }}</div>
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <div class="info-box">{{ $profile['email'] }}</div>
                </div>

                <div class="info-item">
                    <label>Nomor Telepon</label>
                    <div class="info-box">{{ $profile['phone'] }}</div>
                </div>

                <div class="info-item">
                    <label>Alamat</label>
                    <div class="info-box">{{ $profile['address'] }}</div>
                </div>

                <div class="info-item full-width-left">
                    <label>Tanggal Lahir</label>
                    <div class="info-box">{{ $profile['birthdate'] }}</div>
                </div>
            </div>
        </section>

        <div class="profile-action">
            <a href="{{ url('/profile/edit') }}" class="btn-edit">Edit Profil</a>
        </div>
    </main>

</body>
</html>