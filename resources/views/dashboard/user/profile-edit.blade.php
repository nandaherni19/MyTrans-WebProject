<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
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

    <form action="{{ url('/profile/update') }}" method="POST" enctype="multipart/form-data">
        @csrf

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

                    <label for="photoInput" class="camera-badge" style="cursor: pointer;">📷</label>
                    <input type="file" id="photoInput" name="photo" accept="image/*" hidden onchange="this.form.submit()">
            </div>

            <div class="profile-hero-right">
                <h2>{{ $profile['name'] }}</h2>
            </div>
        </section>

        <section class="profile-tabs">
            <a href="{{ url('/profile/edit') }}" class="tab-btn active">Ubah Informasi Pribadi</a>
            <a href="{{ url('/profile/edit/password') }}" class="tab-btn">Ubah Password</a>
        </section>

        <section class="profile-info-card">
            <div class="info-title">
                <h3>Edit Profil</h3>
                <p>Lengkapi identitas Anda</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $profile['name']) }}">
                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $profile['email']) }}">
                    @error('email')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item">
                    <label>Nomor Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $profile['phone']) }}">
                    @error('phone')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item">
                    <label>Alamat</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address', $profile['address']) }}">
                    @error('address')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="info-item full-width-left">
                    <label>Tanggal Lahir</label>
                    <input type="text" name="birthdate" class="form-input" value="{{ old('birthdate', $profile['birthdate']) }}">
                    @error('birthdate')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="edit-action-wrapper">
                <a href="{{ url('/profile') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            </div>
        </section>
    </form>
</main>

</body>
</html>